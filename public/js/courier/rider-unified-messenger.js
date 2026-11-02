/* SARI Rider unified messaging: Buyer, Seller, linked Logistics, Admin Support.
 * Uses authenticated platform messaging APIs and existing Rider/Logistics POST form.
 * No untrusted HTML injection; messages rendered via textContent. */
(function () {
  'use strict';
  const root = document.getElementById('sariRiderMessenger');
  if (!root || root.dataset.initialized === '1') return;
  root.dataset.initialized = '1';
  const $ = id => document.getElementById(id);
  const threadsNode = $('sariRmThreads');
  const messageNode = $('sariRmConversation');
  const searchNode = $('sariRmSearch');
  const contactListNode = $('sariRmContacts');
  const contactOverlay = $('sariRmContactOverlay');
  const contactSearch = $('sariRmContactSearch');
  const feedback = $('sariRmFeedback');
  const bodyInput = $('sariRmBody');
  const attachment = $('sariRmAttachment');
  const sendForm = $('sariRmSendForm');
  const legacyForm = $('sariRmLegacyForm');
  const LEGACY = 'legacy-logistics';
  const allowedRoles = new Set(['buyer', 'social_buyer', 'seller', 'logistics', 'admin']);
  let actor = null;
  let inbox = [];
  let contacts = [];
  let selected = null;
  let activeRole = 'all';
  let contactRole = 'all';
  let currentMessages = [];
  let lastMessageFingerprint = '';
  let inFlight = false;
  let isSending = false;
  let currentRequestSeq = 0;
  let touched = false;
  let drawerFocus = null;
  let contactsFocus = null;
  let loadingLegacy = false;
  let lastLegacyRefresh = 0;

  const escapeUrlPart = value => encodeURIComponent(String(value));
  const category = role => role === 'social_buyer' ? 'buyer' : (role || '');
  const roleLabel = role => ({buyer:'Buyer',social_buyer:'Buyer',seller:'Seller',logistics:'Logistics',admin:'SARI Admin',rider:'Rider'})[role] || 'SARI Contact';
  const initials = name => String(name || 'S').trim().split(/\s+/).slice(0,2).map(x=>x[0]||'').join('').toUpperCase();
  const make = (tag, cls, text) => {
    const node = document.createElement(tag);
    if (cls) node.className = cls;
    if (text !== undefined) node.textContent = text;
    return node;
  };
  const avatar = (name, role, size) => {
    const elt = make('span', 'sari-rm-avatar' + (size ? ' is-'+size : '') + ((role==='admin'||role==='logistics') ? ' is-'+role : ''), initials(name));
    elt.setAttribute('aria-hidden','true');return elt;
  };
  const selectedId = () => selected?.kind === 'platform' ? selected.uuid : (selected?.kind === 'legacy' ? LEGACY : null);
  const isLegacyAvailable = root.dataset.legacyEnabled === '1';
  const isSameOrigin = url => {
    try { const parsed=new URL(url, location.origin);return parsed.origin===location.origin && /^https?:$/.test(parsed.protocol); }
    catch (_) { return false; }
  };
  const formatDate = date => {
    if (!date) return '';
    const d = new Date(date);
    if (Number.isNaN(d.getTime())) return '';
    return d.toLocaleString('en-PH',{timeZone:'Asia/Manila',month:'short',day:'numeric',hour:'numeric',minute:'2-digit'});
  };
  function showFeedback(text) {
    feedback.textContent=text;
    feedback.hidden=!text;
    if (text) window.SariCourierToast?.error?.(text);
  }
  function parseError(data, response) {
    if (data && typeof data === 'object') {
      const first=Object.values(data.errors || {})[0];
      if (Array.isArray(first)&&first.length) return first[0];
      if (typeof data.message === 'string' && data.message && data.message !== 'Server Error') return data.message;
    }
    if (response.status === 419) return 'Your session expired. Refresh and try again.';
    if (response.status === 403) return 'This conversation is not available for your account.';
    if (response.status === 404) return 'Messaging endpoint was not found. Check your Laravel routes.';
    return 'Messaging request failed ('+response.status+').';
  }
  async function api(url, options = {}) {
    const headers={'Accept':'application/json','X-Requested-With':'XMLHttpRequest',...(options.headers||{})};
    if (options.method && options.method !== 'GET') headers['X-CSRF-TOKEN']=root.dataset.csrf;
    if (options.body && !(options.body instanceof FormData)) headers['Content-Type']='application/json';
    const response = await fetch(url,{credentials:'same-origin',cache:'no-store',...options,headers});
    let data=null;
    try {data=await response.json();}catch(_){}
    if (!response.ok) throw new Error(parseError(data,response));
    if (!data) throw new Error('Unexpected server response.');
    return data;
  }
  const apiFor=(uuid, suffix='') => root.dataset.apiBase+'/conversations/'+escapeUrlPart(uuid)+suffix;
  function currentUserParticipant(conversation) {
    return (conversation.participants||[]).filter(p=>!(p.role===actor?.role && Number(p.id)===Number(actor?.id)));
  }
  function detailsFor(conversation) {
    if (conversation.type === 'admin_support' || conversation.context?.type === 'account_support') return {name:'SARI Admin Support',role:'admin'};
    const other=currentUserParticipant(conversation);
    const peer=other.find(p=>allowedRoles.has(p.role)) || other[0];
    return {name:peer?.label || conversation.subject || 'SARI Conversation',role:peer?.role || 'admin'};
  }
  function legacyItem() {const link=contacts.find(c=>c.role==='logistics');return {uuid:LEGACY,kind:'legacy',id:LEGACY,type:'legacy',role:'logistics',name:link?.name||'Linked Logistics',last_message:{body:root.dataset.legacyCount==='0'?'No messages yet':'Existing Logistics conversation'},unread_count:0};}
  function decoratedThreads() {
    const result=inbox.filter(c=>c.uuid && ['direct','admin_support','report_support'].includes(c.type)).map(c=>{
      const peer=detailsFor(c);
      return {...c,kind:'platform',name:peer.name,role:peer.role};
    }).filter(c=>allowedRoles.has(c.role));
    if (isLegacyAvailable) result.unshift(legacyItem());
    return result;
  }
  function threadPasses(item) {
    const query=searchNode.value.trim().toLowerCase();
    if (activeRole!=='all' && category(item.role)!==activeRole) return false;
    return !query || (item.name+' '+roleLabel(item.role)+' '+(item.last_message?.body||'')).toLowerCase().includes(query);
  }
  function renderThreads() {
    const items=decoratedThreads().filter(threadPasses);
    threadsNode.replaceChildren();
    if (!items.length) {
      threadsNode.append(make('p','sari-rm-hint',inbox.length || isLegacyAvailable?'No conversations match this filter.':'No conversations yet. Click + to start one.'));
    }
    for (const item of items) {
      const button=make('button','sari-rm-thread'+(selectedId()===item.uuid?' is-active':''));
      button.type='button';button.dataset.thread=item.uuid;button.setAttribute('role','listitem');
      button.append(avatar(item.name,item.role));
      const copy=make('span','sari-rm-thread-copy');
      copy.append(make('strong','',item.name));
      let summary = item.kind==='legacy' ? 'Original Logistics chat' : (item.last_message?.body || roleLabel(item.role)+' conversation');
      copy.append(make('small','',summary || 'Attachment received'));
      button.append(copy);
      if ((item.unread_count||0)>0) button.append(make('span','sari-rm-unread',String(item.unread_count>99?'99+':item.unread_count)));
      button.addEventListener('click',()=>selectThread(item));
      threadsNode.append(button);
    }
    const unread=inbox.reduce((sum,c)=>sum+Number(c.unread_count||0),0);
    const unreadBadge=$('sariRmUnread');unreadBadge.textContent=unread+' unread';unreadBadge.hidden=!unread;
    document.dispatchEvent(new CustomEvent('sari:rider-message-count',{detail:{unread}}));
  }
  function updateRoleTabs() {
    root.querySelectorAll('[data-rm-role]').forEach(button=>{
      const yes=button.dataset.rmRole===activeRole;
      button.classList.toggle('is-active',yes);button.setAttribute('aria-pressed',String(yes));
    });
  }
  function updateChatHeader(name,role) {
    $('sariRmChatHeader').hidden=false;
    $('sariRmPeerName').textContent=name;
    $('sariRmPeerSubtitle').textContent=role==='admin'?'Official SARI Admin Support':roleLabel(role)+' · Secure conversation';
    const av=$('sariRmPeerAvatar');av.textContent=initials(name);av.className='sari-rm-avatar is-small'+(role==='admin'||role==='logistics'?' is-'+role:'');
    $('sariRmInfoName').textContent=name;
    $('sariRmInfoRole').textContent=roleLabel(role);
    const infoAv=$('sariRmInfoAvatar');infoAv.textContent=initials(name);infoAv.className='sari-rm-avatar is-large'+(role==='admin'||role==='logistics'?' is-'+role:'');
    $('sariRmComposerWrap').hidden=false;
  }
  function messageFingerprint(messages) {
    const last=messages[messages.length-1];
    return messages.length+':'+(last?.id||'')+':'+(last?.created_at||'')+':'+(last?.body||'');
  }
  function renderMessages(messages, force=false) {
    const fingerprint=messageFingerprint(messages);
    if (!force && fingerprint===lastMessageFingerprint) return;
    const nearBottom=messageNode.scrollHeight-messageNode.scrollTop-messageNode.clientHeight<130;
    const wasEmpty=lastMessageFingerprint==='';
    lastMessageFingerprint=fingerprint;
    messageNode.replaceChildren();
    if (!messages.length) {
      const empty=make('div','sari-rm-empty');
      empty.append(make('h3','','Start your conversation'),make('p','','Send a message to coordinate with this SARI contact.'));
      messageNode.append(empty);return;
    }
    for (const msg of messages) {
      const mine=msg.sender_role===actor?.role && Number(msg.sender_id)===Number(actor?.id);
      const who=(typeof msg.sender==='string' ? msg.sender : (msg.sender?.name||msg.sender?.label))||(mine?'You':roleLabel(msg.sender_role));
      const wrap=make('div','sari-rm-message'+(mine?' is-mine':''));
      if (!mine) wrap.append(avatar(who,msg.sender_role,'tiny'));
      const content=make('div','sari-rm-message-body');
      content.append(make('span','sari-rm-sender',mine?'You':who));
      if (msg.body) content.append(make('p','sari-rm-bubble',msg.body));
      const a=msg.attachment;
      if (a && isSameOrigin(a.url)) {
        const link=make('a','sari-rm-attachment');
        link.href=a.url;link.target='_blank';link.rel='noopener noreferrer';
        if (a.is_image){const img=make('img');img.src=a.url;img.alt=a.name||'Shared image';img.loading='lazy';link.append(img);}
        else link.textContent='📎 '+(a.name||'Open attachment');
        content.append(link);
      }
      content.append(make('time','sari-rm-time',formatDate(msg.created_at)));
      wrap.append(content);messageNode.append(wrap);
    }
    if (nearBottom||wasEmpty||force) messageNode.scrollTop=messageNode.scrollHeight;
  }
  async function selectThread(item, opts={}) {
    touched=true;showFeedback('');
    const old=selectedId();
    selected={...item};
    const isLegacy=item.kind==='legacy';
    renderThreads();
    updateChatHeader(item.name,item.role);
    sendForm.hidden=isLegacy;
    legacyForm.hidden=!isLegacy;
    $('sariRmAttachment').value='';$('sariRmFileChip').hidden=true;
    const url=new URL(location.href);
    url.searchParams.set('channel',isLegacy?'logistics':'platform');
    if(isLegacy) url.searchParams.delete('conversation');
    else url.searchParams.set('conversation',item.uuid);
    history.replaceState(history.state,'',url.toString());
    closeDrawer();
    if(isLegacy){
      messageNode.replaceChildren($('sariRmLegacyMessages').content.cloneNode(true));
      messageNode.scrollTop=messageNode.scrollHeight;
      currentMessages=[];lastMessageFingerprint='';
      return;
    }
    if(old!==item.uuid || !opts.keepMessages){messageNode.replaceChildren(make('p','sari-rm-hint','Loading messages…'));lastMessageFingerprint='';}
    await fetchThread(item.uuid, true);
  }
  async function fetchThread(uuid, markRead=false) {
    const seq=++currentRequestSeq;
    try {
      const data=await api(apiFor(uuid));
      if(selectedId()!==uuid || seq!==currentRequestSeq) return;
      currentMessages=Array.isArray(data.messages)?data.messages:[];
      renderMessages(currentMessages);
      if(markRead && document.visibilityState==='visible' && (Number(inbox.find(c=>c.uuid===uuid)?.unread_count||0)>0)) await markThreadRead(uuid);
    } catch(e) {
      if(selectedId()===uuid) showFeedback(e.message);
    }
  }
  async function markThreadRead(uuid) {
    try {await api(apiFor(uuid,'/read'),{method:'POST'});}
    catch(_){return;}
    const item=inbox.find(c=>c.uuid===uuid);
    if(item){item.unread_count=0;renderThreads();}
  }
  async function refreshLegacy() {
    if(loadingLegacy || !isLegacyAvailable || selectedId()!==LEGACY || document.visibilityState!=='visible') return;
    if(Date.now()-lastLegacyRefresh<18000)return;
    loadingLegacy=true;lastLegacyRefresh=Date.now();
    try {
      const url=new URL(root.dataset.legacyUrl,location.origin);
      const response=await fetch(url.toString(),{credentials:'same-origin',cache:'no-store',headers:{'Accept':'text/html'}});
      if(!response.ok)return;
      const html=await response.text();
      const doc=new DOMParser().parseFromString(html,'text/html');
      const remote=doc.querySelector('#sariRmLegacyMessages');
      if(!remote || selectedId()!==LEGACY)return;
      const current=$('sariRmLegacyMessages');
      const previous=current.innerHTML;
      if(remote.innerHTML!==previous){
        const nearBottom=messageNode.scrollHeight-messageNode.scrollTop-messageNode.clientHeight<140;
        current.innerHTML=remote.innerHTML;
        messageNode.replaceChildren(current.content.cloneNode(true));
        if(nearBottom)messageNode.scrollTop=messageNode.scrollHeight;
      }
    }catch(_){}finally{loadingLegacy=false;}
  }
  async function refreshInbox(force=false) {
    if(inFlight)return;
    inFlight=true;
    try {
      const data=await api(root.dataset.inboxUrl);
      inbox=Array.isArray(data.conversations)?data.conversations:[];
      renderThreads();
      $('sariRmInboxStatus').textContent='Secure SARI conversations';
      if (selected?.kind==='platform' && !inbox.some(c=>c.uuid===selected.uuid)) {
        selected=null;updateBlank();
      }
      if(!touched && force){
        const qs=new URLSearchParams(location.search);
        const target=qs.get('conversation');
        const saved=inbox.find(c=>c.uuid===target);
        if(qs.get('channel')==='logistics' && isLegacyAvailable) await selectThread(legacyItem());
        else if(saved) await selectThread({...saved,...detailsFor(saved),kind:'platform'});
        else if(inbox.length) {
          const candidate=decoratedThreads().find(c=>c.kind==='platform');
          if(candidate) await selectThread(candidate);
        } else if(isLegacyAvailable) await selectThread(legacyItem());
      }
    }catch(e){
      if(force){threadsNode.replaceChildren(make('p','sari-rm-hint',e.message));showFeedback(e.message);}
    }finally{inFlight=false;}
  }
  function updateBlank() {
    $('sariRmChatHeader').hidden=true;$('sariRmComposerWrap').hidden=true;
    messageNode.replaceChildren(make('div','sari-rm-empty','Choose a conversation to begin.'));
  }
  function contactPasses(item) {
    if(!allowedRoles.has(item.role))return false;
    if(contactRole!=='all' && category(item.role)!==contactRole)return false;
    const q=contactSearch.value.trim().toLowerCase();
    return !q || (String(item.name||'')+' '+roleLabel(item.role)).toLowerCase().includes(q);
  }
  function allContacts() {
    const unique=new Map();
    contacts.filter(c=>allowedRoles.has(c.role)&&c.role!=='admin'&&c.role!=='logistics').forEach(c=>unique.set(c.role+':'+c.id,c));
    if(isLegacyAvailable) unique.set('logistics:legacy',{role:'logistics',id:LEGACY,name:legacyItem().name,legacy:true});
    return [{role:'admin',id:'support',name:'SARI Admin Support',support:true},...unique.values()];
  }
  function renderContacts(){
    contactListNode.replaceChildren();
    const results=allContacts().filter(contactPasses);
    if(!results.length){contactListNode.append(make('p','sari-rm-hint','No available contacts match this filter.'));return;}
    for(const contact of results){
      const btn=make('button','sari-rm-contact-option');btn.type='button';
      btn.append(avatar(contact.name,contact.role));
      const text=make('span','sari-rm-thread-copy');
      text.append(make('strong','',contact.name),make('small','',contact.support?'Official SARI Administrator conversation':roleLabel(contact.role)));
      btn.append(text);
      btn.addEventListener('click',()=>openContact(contact,btn));
      contactListNode.append(btn);
    }
  }
  async function openContact(contact,button){
    if(button.disabled)return;
    button.disabled=true;showFeedback('');
    try {
      let data;
      if(contact.legacy){closeContacts();await selectThread(legacyItem());return;}
      if(contact.support) data=await api(root.dataset.openSupportUrl,{method:'POST'});
      else data=await api(root.dataset.openDirectUrl,{method:'POST',body:JSON.stringify({target_role:contact.role,target_id:Number(contact.id)})});
      if(!data.conversation?.uuid)throw new Error('Conversation could not be opened.');
      closeContacts();
      await refreshInbox();
      const entry=inbox.find(c=>c.uuid===data.conversation.uuid)||{...data.conversation,participants:[{role:contact.role,id:contact.id,label:contact.name}]};
      await selectThread({...entry,kind:'platform',name:contact.name,role:contact.role});
      bodyInput.focus();
    }catch(e){showFeedback(e.message);button.disabled=false;}
  }
  async function loadContacts(){
    try {
      const data=await api(root.dataset.contactsUrl);
      actor=data.actor;
      contacts=Array.isArray(data.contacts)?data.contacts:[];
      renderContacts();
    }catch(e){contactListNode.replaceChildren(make('p','sari-rm-hint',e.message));}
  }
  function openContacts(){
    contactsFocus=document.activeElement;
    contactOverlay.hidden=false;contactSearch.value='';contactRole='all';renderContactTabs();renderContacts();
    contactSearch.focus();
  }
  function closeContacts(){contactOverlay.hidden=true;contactsFocus?.focus?.();}
  function renderContactTabs(){root.querySelectorAll('[data-rm-contact-role]').forEach(btn=>btn.classList.toggle('is-active',btn.dataset.rmContactRole===contactRole));}
  function openDrawer(){
    if(!selected)return;
    drawerFocus=document.activeElement;
    const drawer=$('sariRmInfoDrawer');drawer.classList.add('is-open');drawer.inert=false;drawer.setAttribute('aria-hidden','false');
    $('sariRmBackdrop').hidden=false;$('sariRmInfoButton').setAttribute('aria-expanded','true');$('sariRmDrawerClose').focus();
  }
  function closeDrawer(){
    const drawer=$('sariRmInfoDrawer');drawer.classList.remove('is-open');drawer.inert=true;drawer.setAttribute('aria-hidden','true');
    $('sariRmBackdrop').hidden=true;$('sariRmInfoButton').setAttribute('aria-expanded','false');
  }
  function fileInfo(){
    const file=attachment.files?.[0];
    $('sariRmFileChip').hidden=!file;
    $('sariRmFileName').textContent=file ? file.name+' · '+(file.size/1024/1024).toFixed(1)+' MB':'';
    if(file && file.size>15*1024*1024){showFeedback('Maximum attachment size is 15 MB.');attachment.value='';$('sariRmFileChip').hidden=true;}
  }
  async function send(event){
    event.preventDefault();
    if(!selected||selected.kind!=='platform'||isSending)return;
    const content=bodyInput.value.trim();const file=attachment.files?.[0];
    if(!content&&!file){showFeedback('Write a message or attach a file.');bodyInput.focus();return;}
    if(file&&file.size>15*1024*1024){showFeedback('Maximum attachment size is 15 MB.');return;}
    isSending=true;$('sariRmSend').disabled=true;showFeedback('');
    const fd=new FormData();if(content)fd.append('body',content);if(file)fd.append('attachment',file);
    const uuid=selected.uuid;
    try {
      const response=await api(apiFor(uuid,'/messages'),{method:'POST',body:fd});
      if(selectedId()!==uuid)return;
      bodyInput.value='';bodyInput.style.height='';attachment.value='';fileInfo();
      if(response.message?.id && !currentMessages.some(m=>m.id===response.message.id)) currentMessages.push(response.message);
      renderMessages(currentMessages,true);
      await refreshInbox();
    } catch(e){showFeedback(e.message);}finally{isSending=false;$('sariRmSend').disabled=false;}
  }
  function wireEvents(){
    root.querySelectorAll('[data-rm-role]').forEach(btn=>btn.addEventListener('click',()=>{activeRole=btn.dataset.rmRole;updateRoleTabs();renderThreads();}));
    searchNode.addEventListener('input',renderThreads);
    $('sariRmNew').addEventListener('click',openContacts);
    $('sariRmContactClose').addEventListener('click',closeContacts);
    contactOverlay.addEventListener('click',e=>{if(e.target===contactOverlay)closeContacts();});
    contactSearch.addEventListener('input',renderContacts);
    root.querySelectorAll('[data-rm-contact-role]').forEach(btn=>btn.addEventListener('click',()=>{contactRole=btn.dataset.rmContactRole;renderContactTabs();renderContacts();}));
    $('sariRmInfoButton').addEventListener('click',()=>{if($('sariRmInfoDrawer').classList.contains('is-open'))closeDrawer();else openDrawer();});
    $('sariRmDrawerClose').addEventListener('click',()=>{closeDrawer();drawerFocus?.focus?.();});
    $('sariRmBackdrop').addEventListener('click',closeDrawer);
    root.addEventListener('keydown',e=>{
      if(e.key==='Escape') {if(!contactOverlay.hidden)closeContacts();else if($('sariRmInfoDrawer').classList.contains('is-open')){closeDrawer();drawerFocus?.focus?.();}}
      if(e.key==='Tab'&&!contactOverlay.hidden){
        const nodes=[...contactOverlay.querySelectorAll('button:not(:disabled),input')].filter(n=>n.offsetParent!==null);
        if(!nodes.length)return;const first=nodes[0],last=nodes[nodes.length-1];
        if(e.shiftKey&&document.activeElement===first){e.preventDefault();last.focus();}
        else if(!e.shiftKey&&document.activeElement===last){e.preventDefault();first.focus();}
      }
    });
    sendForm.addEventListener('submit',send);
    attachment.addEventListener('change',fileInfo);
    $('sariRmFileRemove').addEventListener('click',()=>{attachment.value='';fileInfo();});
    bodyInput.addEventListener('keydown',e=>{if(e.key==='Enter'&&!e.shiftKey&&!e.isComposing){e.preventDefault();sendForm.requestSubmit();}});
    bodyInput.addEventListener('input',()=>{bodyInput.style.height='';bodyInput.style.height=Math.min(bodyInput.scrollHeight,120)+'px';});
    legacyForm.addEventListener('submit',e=>{
      const value=$('sariRmLegacyBody').value.trim();
      if(!value){e.preventDefault();showFeedback('Write a message to Logistics.');return;}
      legacyForm.querySelector('button[type="submit"]').disabled=true;
    });
  }
  async function initialize(){
    wireEvents();
    await loadContacts();
    await refreshInbox(true);
    if(!actor) {try{actor=(await api(root.dataset.selfUrl)).actor;}catch(_){}}
    if(selected?.kind==='platform') await fetchThread(selected.uuid,true);
    window.setInterval(()=>{
      if(document.visibilityState!=='visible')return;
      refreshInbox();
      if(selected?.kind==='platform'&&!isSending)fetchThread(selected.uuid,true);
      if(selected?.kind==='legacy')refreshLegacy();
    },12000);
    document.addEventListener('visibilitychange',()=>{
      if(document.visibilityState==='visible'){refreshInbox();if(selected?.kind==='platform')fetchThread(selected.uuid,true);else if(selected?.kind==='legacy')refreshLegacy();}
    });
  }
  initialize();
})();
