@extends('layouts.seller')
@section('title','Edit Size Chart — SARI')
@section('page-title','Product Size Chart')
@push('styles')
<style>
.sc-editor{max-width:1200px;margin:auto;padding:24px 28px 40px;font-family:Poppins,ui-sans-serif,system-ui,sans-serif;color:var(--sari-dark-e23e64dd1e, #2f3742)}.sc-editor *{box-sizing:border-box}.sc-editor h1{font-size:28px;line-height:1.2;margin:6px 0 8px;font-weight:750}.sc-editor .sc-muted{font-size:12px;color:var(--sari-dark-a63aa16d7c, #7b8794);line-height:1.7}.sc-editor .sc-card{margin-top:20px;border:1px solid var(--sari-dark-d8c2716225, #e5e7eb);border-radius:18px;padding:20px;background:var(--sari-dark-950baec030, #fff);box-shadow:0 3px 10px var(--sari-dark-7593018246, rgba(15,23,42,.035)),0 12px 30px var(--sari-dark-fbd3b3b9f6, rgba(15,23,42,.055))}.sc-editor .sc-fields{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:14px}.sc-editor label{display:grid;gap:7px;font-size:12px;font-weight:600}.sc-editor input,.sc-editor select{width:100%;min-width:0;min-height:44px;border:1px solid var(--sari-dark-d8c2716225, #dce2e8);border-radius:10px;background:var(--sari-dark-950baec030, white);padding:9px 11px;color:var(--sari-dark-e23e64dd1e, #475569);font:inherit;font-size:12px}.sc-editor input:focus-visible,.sc-editor select:focus-visible,.sc-editor button:focus-visible,.sc-editor a:focus-visible{outline:2px solid #c88912;outline-offset:3px}.sc-editor button{border:1px solid var(--sari-dark-d8c2716225, #dce2e8);border-radius:10px;background:var(--sari-dark-950baec030, white);padding:10px 15px;min-height:40px;font:600 12px Poppins,sans-serif;color:var(--sari-dark-a63aa16d7c, #59636e);cursor:pointer}.sc-editor .sc-gold{background:#c88912;border-color:#c88912;color:white}.sc-editor button:hover{border-color:#c88912}.sc-editor .sc-actions{display:flex;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-top:18px;align-items:center}.sc-editor table{border-collapse:collapse;min-width:720px;width:100%;font-size:11px;text-align:left}.sc-editor th{padding:12px 8px;background:var(--sari-dark-950baec030, #f8fafc);color:var(--sari-dark-a63aa16d7c, #7b8794);font-weight:650}.sc-editor td{padding:10px 8px;border-bottom:1px solid var(--sari-dark-d8c2716225, #edf0f3);vertical-align:top}.sc-editor td:first-child{width:140px}.sc-editor .sc-range{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:6px}.sc-editor .sc-range label{font-size:10px;color:var(--sari-dark-a63aa16d7c, #8793a1)}.sc-editor .sc-range input{width:100%;min-width:70px;min-height:38px;padding:8px}.sc-editor .sc-scroll{overflow-x:auto;margin-top:16px}.sc-editor .sc-notice{border:1px solid var(--sari-dark-d8c2716225, #dce2e8);border-radius:12px;padding:12px 16px;background:var(--sari-dark-950baec030, white);font-size:12px;line-height:1.7}.sc-editor .sc-error{color:var(--sari-dark-5300031fd1, #b91c1c)}.sc-editor .sc-success{color:var(--sari-dark-e23e64dd1e, #166534)}.sc-editor a{color:var(--sari-dark-685eb4fc40, #a87316)}.sc-editor .sc-ease[hidden]{display:none}.sc-editor .sc-preview{white-space:pre-wrap;font-size:12px;line-height:1.8;color:var(--sari-dark-a63aa16d7c, #667383);margin-top:14px}@media(max-width:767px){.sc-editor{padding:20px 16px 32px}.sc-editor .sc-fields{grid-template-columns:repeat(2,minmax(0,1fr))}.sc-editor h1{font-size:25px}.sc-editor .sc-card{padding:16px}}
</style>
@endpush
@section('content')
@php
    $default = $chart ? $chart->only(['unit','measurement_type','garment_type','fit','rows']) : ['unit'=>'cm','measurement_type'=>'body_range','garment_type'=>'top','fit'=>'regular','rows'=>[]];
    $submitted = old('chart_payload');
    $initial = is_string($submitted) ? json_decode($submitted,true) : $default;
    $initial = is_array($initial) ? $initial : $default;
@endphp
<main class="sc-editor">
    <p style="font-size:10.5px;font-weight:800;letter-spacing:.14em;color:var(--sari-dark-685eb4fc40, #a87316);margin:0">PRODUCT MEASUREMENTS</p>
    <h1>{{ $product->name }}</h1>
    <p class="sc-muted">Use measurements verified for this exact product. Existing chart images stay unchanged. Size matching supports chest/waist/hips circumference and foot length.</p>
    <a href="{{ route('seller.products.size-charts') }}" style="font-size:12px">‹ Back to Size Charts</a>
    @if(session('success'))<p class="sc-notice sc-success" role="status">{{ session('success') }}</p>@endif
    @if($errors->any())<div class="sc-notice sc-error" role="alert"><strong>Check the measurements</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
    @if($product->size_guide_image_path)
        <details class="sc-card"><summary style="font-size:12px;font-weight:650;cursor:pointer">Reference: existing seller chart image</summary>
            <img src="{{ route('seller.products.size-guide',$product) }}" alt="Existing seller size chart for {{ $product->name }}" loading="lazy" style="display:block;max-width:640px;width:100%;height:auto;margin-top:14px">
            <p class="sc-muted">Transcribe verified measurements. No values are automatically guessed from this image.</p>
        </details>
    @endif
    @if(!$sizeLabels)
        <div class="sc-card"><p class="sc-muted">Add active Size or Shoe Size variants to this product before creating its structured chart.</p><a href="{{ route('seller.products.index') }}">Open Products</a></div>
    @else
    <form id="sellerStructuredChart" action="{{ route('seller.products.size-chart.update',$product) }}" method="POST" class="sc-card">
        @csrf @method('PUT')
        <input type="hidden" name="chart_payload" id="scPayload">
        <div class="sc-fields">
            <label>Product type<select id="scType"><option value="top">Top / Shirt</option><option value="bottom">Bottom / Shorts / Pants</option><option value="dress">Dress</option><option value="footwear">Footwear</option></select></label>
            <label>Measurement unit<select id="scUnit"><option value="cm">Centimeters (cm)</option><option value="in">Inches (in)</option></select></label>
            <label>Chart basis<select id="scBasis"><option value="body_range">Buyer body ranges</option><option value="garment">Garment measurements</option></select></label>
            <label>Intended fit<select id="scFit"><option value="regular">Regular</option><option value="slim">Slim</option><option value="relaxed">Relaxed</option></select></label>
        </div>
        <p id="scBasisHelp" class="sc-muted"></p>
        <p class="sc-muted">Body and garment chest/waist/hips must be full circumference, not laid-flat width. Foot measurements are length. Min = Max is allowed for exact measurements. Each size label must match an active variant.</p>
        <div class="sc-scroll"><table><thead><tr id="scHead"></tr></thead><tbody id="scRows"></tbody></table></div>
        <div class="sc-actions"><button id="scAdd" type="button">+ Add size row</button><button class="sc-gold" type="submit">Save size chart</button></div>
        <p id="scEditorError" class="sc-error" role="alert"></p>
        <div id="scPreview" class="sc-preview" aria-live="polite"></div>
    </form>
    @if($chart)<form method="POST" action="{{ route('seller.products.size-chart.destroy',$product) }}" style="margin-top:20px" onsubmit="return confirm('Remove the structured measurements? Existing size chart images will stay.');">@csrf @method('DELETE')<button type="submit">Remove structured chart</button></form>@endif
    @endif
</main>
@if($sizeLabels)
<script>
(() => {
    const form=document.getElementById('sellerStructuredChart');if(!form||form.dataset.bound)return;form.dataset.bound='1';
    const initial=@json($initial), labels=@json($sizeLabels);
    const type=document.getElementById('scType'),unit=document.getElementById('scUnit'),basis=document.getElementById('scBasis'),fit=document.getElementById('scFit'),rows=document.getElementById('scRows');
    for(const [el,key] of [[type,'garment_type'],[unit,'unit'],[basis,'measurement_type'],[fit,'fit']])if(Array.from(el.options).some(o=>o.value===initial[key]))el.value=initial[key];
    let data=Array.isArray(initial.rows)?initial.rows.filter(row=>row&&typeof row==='object'&&!Array.isArray(row)).map(row=>({size:typeof row.size==='string'?row.size:'',measurements:row.measurements&&typeof row.measurements==='object'?row.measurements:{}})):[];let previousUnit=unit.value;
    const dimensions=()=>({top:['chest'],bottom:['waist','hips'],dress:['chest','waist','hips'],footwear:['foot']}[type.value]);
    const title=name=>name==='foot'?'Foot length':name.charAt(0).toUpperCase()+name.slice(1);
    function read(){return Array.from(rows.children).map(tr=>{const measurements={};tr.querySelectorAll('[data-dimension]').forEach(box=>{const values={};box.querySelectorAll('input').forEach(input=>{if(input.value!=='')values[input.dataset.key]=Number(input.value);});if(Object.keys(values).length)measurements[box.dataset.dimension]=values;});return {size:tr.querySelector('select').value,measurements};});}
    function preview(){const count=rows.children.length;document.getElementById('scPreview').textContent=count+' size row(s) · '+unit.value+' · '+(basis.value==='body_range'?'Buyer body ranges':'Garment measurements with seller-defined ease')+' · '+fit.value+' fit';}
    function render(){
        const head=document.getElementById('scHead');head.replaceChildren();for(const text of ['Size',...dimensions().map(d=>title(d)+' ('+unit.value+')'),'Action']){const th=document.createElement('th');th.scope='col';th.textContent=text;head.append(th);}
        rows.replaceChildren();data.forEach((row,index)=>{
            const tr=document.createElement('tr'),sizeCell=document.createElement('td'),select=document.createElement('select');select.required=true;select.setAttribute('aria-label','Size row '+(index+1));
            const empty=document.createElement('option');empty.value='';empty.textContent='Select size';select.append(empty);
            for(const label of labels){const option=document.createElement('option');option.value=label;option.textContent=label;select.append(option);}select.value=row.size||'';sizeCell.append(select);tr.append(sizeCell);
            dimensions().forEach(d=>{const td=document.createElement('td'),box=document.createElement('div');box.className='sc-range';box.dataset.dimension=d;
                for(const key of basis.value==='garment'?['min','max','ease_min','ease_max']:['min','max']){const label=document.createElement('label');label.textContent=key.replace('_',' ');const input=document.createElement('input');input.type='number';input.required=true;input.step='0.01';input.min=key.startsWith('ease')?'0':'0.1';input.max=key.startsWith('ease')?'50':'300';input.dataset.key=key;input.value=row.measurements?.[d]?.[key]??'';input.setAttribute('aria-label','Row '+(index+1)+' '+title(d)+' '+key.replace('_',' '));label.append(input);box.append(label);}td.append(box);tr.append(td);});
            const action=document.createElement('td'),remove=document.createElement('button');remove.type='button';remove.textContent='Remove';remove.setAttribute('aria-label','Remove size row '+(index+1));remove.onclick=()=>{data=read();data.splice(index,1);render();};action.append(remove);tr.append(action);rows.append(tr);
        });
        document.getElementById('scBasisHelp').textContent=basis.value==='body_range'?'Enter the buyer body measurement range that fits each size. Matching does not add extra allowance.':'Enter actual garment measurements AND the minimum/maximum extra room (ease) suitable for your intended fit. SARI will not invent this allowance.';preview();
    }
    document.getElementById('scAdd').onclick=()=>{data=read();if(data.length>=40)return;data.push({size:'',measurements:{}});render();};
    type.onchange=()=>{data=read();render();};basis.onchange=()=>{data=read();render();};fit.onchange=preview;
    unit.onchange=()=>{data=read();const factor=previousUnit==='in'?2.54:1/2.54;data.forEach(row=>Object.values(row.measurements).forEach(range=>Object.keys(range).forEach(key=>range[key]=Math.round(range[key]*factor*100)/100)));previousUnit=unit.value;render();};
    form.addEventListener('input',preview);
    form.onsubmit=event=>{const result=read();if(!result.length){event.preventDefault();document.getElementById('scEditorError').textContent='Add at least one size row.';return;}document.getElementById('scPayload').value=JSON.stringify({unit:unit.value,measurement_type:basis.value,garment_type:type.value,fit:fit.value,rows:result});};
    if(!data.length)data=[{size:'',measurements:{}}];render();
})();
</script>
@endif
@endsection
