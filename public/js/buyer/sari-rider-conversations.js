/* SARI Buyer: reply to Rider-initiated universal messaging threads. */
(function(){
 'use strict';
 const root=document.getElementById('sariBuyerRiderInbox');if(!root||root.dataset.loaded==='1')return;root.dataset.loaded='1';
 const $=id=>document.getElementById(id),base=root.dataset.apiBase;
 const state={actor:null,conversations:[],uuid:null,messages:[],busy:false,unread:0};
 const node=(tag,cls,text)=>{const n=document.createElement(tag);if(cls)n.className=cls;if(text!==undefined)n.textContent=text;return n;};
 const isMine=(m)=>state.actor&&m.sender_role===state.actor.role&&Number(m.sender_id)===Number(state.actor.id);
 const rider=(c)=>{const participants=c.participants||[];const p=participants.find(x=>x.role==='rider');return p||null;};
 const err=(text)=>{$('sariBrmError').textContent=text;$('sariBrmError').hidden=!text;};
 const format=val=>{try{return new Date(val).toLocaleString('en-PH',{timeZone:'Asia/Manila',month:'short',day:'numeric',hour:'numeric',minute:'2-digit'});}catch(_){return '';}};
 async function api(path,options={}){
   const headers={'Accept':'application/json','X-Requested-With':'XMLHttpRequest',...options.headers};
   if(options.method==='POST')headers['X-CSRF-TOKEN']=root.dataset.csrf;
   if(options.body && !(options.body instanceof FormData))headers['Content-Type']='application/json';
   const response=await fetch(base+path,{credentials:'same-origin',cache:'no-store',...options,headers});
   const data=await response.json().catch(()=>null);
   if(!response.ok){const messages=Object.values(data?.errors||{})[0];throw new Error((Array.isArray(messages)&&messages[0])||data?.message||'Unable to load Rider messages.');}
   return data;
 }
 function inboxRows(){return state.conversations.filter(c=>['direct','admin_support','report_support'].includes(c.type)&&rider(c));}
 function renderInbox(){
   const list=$('sariBrmThreads');list.replaceChildren();
   const q=$('sariBrmSearch').value.trim().toLowerCase();const rows=inboxRows().filter(c=>rider(c).label.toLowerCase().includes(q));
   if(!rows.length){list.append(node('p','','No Rider conversations yet. A Rider can start a private conversation with you.'));return;}
   rows.forEach(c=>{
      const p=rider(c);const button=node('button','sari-brm-thread'+(c.uuid===state.uuid?' is-active':''));button.type='button';
      button.append(node('span','sari-brm-avatar',p.label.trim().split(/\s+/).slice(0,2).map(s=>s[0]).join('').toUpperCase()));
      const copy=node('span','sari-brm-thread-copy');copy.append(node('strong','',p.label),node('small','',c.last_message?.body||'Rider conversation'));button.append(copy);
      if(Number(c.unread_count||0)>0)button.append(node('span','sari-brm-unread',String(c.unread_count)));
      button.addEventListener('click',()=>open(c.uuid));list.append(button);
   });
 }
 function renderMessages(){
   const target=$('sariBrmMessages'),nearBottom=target.scrollHeight-target.scrollTop-target.clientHeight<130;
   target.replaceChildren();
   if(!state.messages.length){const e=node('div','sari-brm-empty');e.append(node('h3','','Start your conversation'),node('p','','Reply to the Rider about your delivery.'));target.append(e);return;}
   for(const msg of state.messages){const mine=isMine(msg),row=node('div','sari-brm-row'+(mine?' is-mine':'')),bubble=node('div','sari-brm-message');
     bubble.append(node('strong','',mine?'You':typeof msg.sender==='string'?msg.sender:'Rider'));
     if(msg.body)bubble.append(node('p','',msg.body));
     if(msg.attachment?.url){try{const dest=new URL(msg.attachment.url,location.origin);if(dest.origin===location.origin){const a=node('a','','📎 '+(msg.attachment.name||'Attachment'));a.href=dest.href;a.target='_blank';a.rel='noopener noreferrer';bubble.append(a);}}catch(_){}}
     bubble.append(node('time','',format(msg.created_at)));row.append(bubble);target.append(row);
   }
   if(nearBottom)target.scrollTop=target.scrollHeight;
 }
 async function open(uuid){
   state.uuid=uuid;err('');renderInbox();
   const item=state.conversations.find(c=>c.uuid===uuid),peer=rider(item);
   $('sariBrmHeader').hidden=false;$('sariBrmAvatar').textContent=(peer?.label||'R').slice(0,1).toUpperCase();$('sariBrmName').textContent=peer?.label||'Rider';$('sariBrmReply').hidden=false;
   try{const data=await api('/conversations/'+encodeURIComponent(uuid));if(state.uuid!==uuid)return;
       state.messages=data.messages||[];renderMessages();
       if(Number(item.unread_count||0)>0){await api('/conversations/'+encodeURIComponent(uuid)+'/read',{method:'POST'});item.unread_count=0;renderInbox();}
   }catch(e){err(e.message);}
 }
 async function refresh(){
   try{if(!state.actor)state.actor=(await api('/me')).actor;
       state.conversations=(await api('/conversations')).conversations||[];renderInbox();
       if(state.uuid){const c=state.conversations.find(x=>x.uuid===state.uuid);if(c)await open(state.uuid);}
       else if(inboxRows().length)await open(inboxRows()[0].uuid);
   }catch(e){err(e.message);}
 }
 $('sariBrmSearch').addEventListener('input',renderInbox);
 $('sariBrmReply').addEventListener('submit',async e=>{
   e.preventDefault();if(state.busy||!state.uuid)return;const text=$('sariBrmBody').value.trim();if(!text){err('Please enter a message.');return;}
   state.busy=true;$('sariBrmSend').disabled=true;err('');
   try{await api('/conversations/'+encodeURIComponent(state.uuid)+'/messages',{method:'POST',body:JSON.stringify({body:text})});$('sariBrmBody').value='';await refresh();}
   catch(x){err(x.message);}finally{state.busy=false;$('sariBrmSend').disabled=false;}
 });
 $('sariBrmBody').addEventListener('keydown',e=>{if(e.key==='Enter'&&!e.shiftKey&&!e.isComposing){e.preventDefault();$('sariBrmReply').requestSubmit();}});
 refresh();window.setInterval(()=>{if(document.visibilityState==='visible'&&!state.busy)refresh();},12000);
})();
