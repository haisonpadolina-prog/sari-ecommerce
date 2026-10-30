{{-- Focused facility editor. Kept hidden until + New Hub; edit/errors reopen automatically. --}}
<section id="hubEditor" class="fe-workspace scroll-mt-24" @if(!$editing && !$errors->any()) hidden @endif>
    <header class="fe-header">
        <div class="fe-heading">
            <p class="fe-kicker">{{ $editing ? 'Edit Sorting Hub' : 'Create Sorting Hub' }}</p>
            <h3 class="fe-title" id="facilityEditorTitle">{{ $editing ? $editing->name : 'Sorting Hub Setup' }}</h3>
            <p class="fe-intro">Set up the facility address and precise location for parcel routing.</p>
        </div>
        <div class="fe-header-actions">
            <button type="button" id="facilityHelpOpen" class="fe-help-trigger" aria-label="Branch access and form help" aria-haspopup="dialog" aria-controls="facilityHelpDialog" title="Branch access and setup guide">
                <svg viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M9.8 9a2.4 2.4 0 1 1 3.4 2.2c-.9.5-1.2 1-1.2 2"/><path d="M12 17h.01"/></svg>
            </button>
            @if($editing)
                <a href="{{ route('logistics.facilities.index') }}" class="fe-close">Cancel Edit</a>
            @else
                <button type="button" id="hubEditorClose" class="fe-close">Close</button>
            @endif
        </div>
    </header>

    <form id="sariFacilityForm" method="POST" action="{{ $editing ? route('logistics.facilities.update', $editing) : route('logistics.facilities.store') }}" class="fe-form">
        @csrf
        @if($editing) @method('PATCH') @endif

        <div class="fe-section-head">
            <span class="fe-section-number">01</span>
            <div><h4>Hub Details</h4><p>Required fields are marked with *.</p></div>
        </div>

        <div class="fe-fields">
            <div class="fe-field">
                <label for="facilityName">Hub Name <span>*</span></label>
                <input id="facilityName" name="name" type="text" maxlength="140" minlength="3" required autocomplete="organization" value="{{ old('name', $editing?->name) }}" placeholder="e.g. SARI Calamba Hub" aria-describedby="facilityNameHint facilityNameError" class="fe-control" data-facility-validate="name">
                <p id="facilityNameHint" class="fe-field-hint">Use the facility's name. Letters and numbers are allowed.</p>
                <p id="facilityNameError" class="fe-error" role="alert" hidden></p>
            </div>

            <div class="fe-field">
                <label for="facilityAddress">Full Address / Landmark <span>*</span></label>
                <textarea id="facilityAddress" name="address" rows="2" maxlength="1200" minlength="8" required autocomplete="street-address" placeholder="Building, street, barangay, landmark..." aria-describedby="facilityAddressHint facilityAddressError" class="fe-control fe-textarea" data-facility-validate="address">{{ old('address', $editing?->address) }}</textarea>
                <p id="facilityAddressHint" class="fe-field-hint">Include building or street number and a recognizable landmark.</p>
                <p id="facilityAddressError" class="fe-error" role="alert" hidden></p>
            </div>

            <p id="facilityAddressStatus" role="status" aria-live="polite" hidden class="fe-address-status">Address directory loads when the form opens.</p>

            <div class="fe-location-grid">
                <div class="fe-field hub-address-wrap">
                    <label for="facility-province-search">Province / Area <span>*</span></label>
                    <input id="facility-province-search" type="text" maxlength="120" required autocomplete="off" spellcheck="false" placeholder="Select province..." value="{{ old('province_name', $editing?->province_name) }}" class="fe-control" data-location-name="province" aria-describedby="facilityProvinceHint facilityProvinceError">
                    <input id="facility-province-name" type="hidden" name="province_name" value="{{ old('province_name', $editing?->province_name) }}">
                    <input id="facility-province-code" type="hidden" name="province_code">
                    <div id="facility-province-results" class="hub-address-results" hidden></div>
                    <p id="facilityProvinceHint" class="fe-field-hint">Select an option from the directory.</p>
                    <p id="facilityProvinceError" class="fe-error" role="alert" hidden></p>
                </div>
                <div class="fe-field hub-address-wrap">
                    <label for="facility-municipality-search">City / Municipality <span>*</span></label>
                    <input id="facility-municipality-search" type="text" maxlength="120" required autocomplete="off" spellcheck="false" placeholder="Choose province first" value="{{ old('municipality_name', $editing?->municipality_name) }}" class="fe-control" data-location-name="municipality" aria-describedby="facilityMunicipalityHint facilityMunicipalityError" disabled>
                    <input id="facility-municipality-name" type="hidden" name="municipality_name" value="{{ old('municipality_name', $editing?->municipality_name) }}">
                    <input id="facility-municipality-code" type="hidden" name="municipality_code">
                    <div id="facility-municipality-results" class="hub-address-results" hidden></div>
                    <p id="facilityMunicipalityHint" class="fe-field-hint">Choose a province to see its cities.</p>
                    <p id="facilityMunicipalityError" class="fe-error" role="alert" hidden></p>
                </div>
            </div>

            <div class="fe-field hub-address-wrap">
                <label for="facility-barangay-search">Barangay <em>(optional)</em></label>
                <input id="facility-barangay-search" type="text" maxlength="120" autocomplete="off" spellcheck="false" placeholder="Choose municipality first" value="{{ old('barangay_name', $editing?->barangay_name) }}" class="fe-control" data-location-name="barangay" aria-describedby="facilityBarangayHint facilityBarangayError" disabled>
                <input id="facility-barangay-name" type="hidden" name="barangay_name" value="{{ old('barangay_name', $editing?->barangay_name) }}">
                <input id="facility-barangay-code" type="hidden" name="barangay_code">
                <div id="facility-barangay-results" class="hub-address-results" hidden></div>
                <p id="facilityBarangayHint" class="fe-field-hint">Optional. Choose from suggestions, or leave blank.</p>
                <p id="facilityBarangayError" class="fe-error" role="alert" hidden></p>
            </div>

            <div class="fe-field fe-map-field">
                <div class="fe-map-header">
                    <div>
                        <p id="facilityMapLabel" class="fe-map-label">Exact Hub Location <span>*</span></p>
                        <p class="fe-field-hint">Search a landmark or select the exact facility entrance on the map.</p>
                    </div>
                    <span id="sariFacilityMapStatus" class="fe-map-status {{ old('latitude', $editing?->latitude) && old('longitude', $editing?->longitude) ? 'is-set' : '' }}">{{ old('latitude', $editing?->latitude) && old('longitude', $editing?->longitude) ? 'LOCATION SAVED' : 'NOT SELECTED' }}</span>
                </div>

                <div class="fe-place-search">
                    <label class="sr-only" for="facilityPlaceSearch">Search facility address or landmark</label>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/></svg>
                    <input type="search" id="facilityPlaceSearch" placeholder="Search building, landmark, or street..." autocomplete="off" aria-controls="facilityPlaceResults" aria-describedby="facilityPlaceHelp" maxlength="180">
                    <button id="facilityPlaceSearchButton" type="button">Find</button>
                </div>
                <p id="facilityPlaceHelp" class="fe-field-hint">Search, choose a result, then adjust the pin on the map if needed.</p>
                <div id="facilityPlaceResults" class="fe-place-results" role="list" hidden></div>
                <p id="facilityPlaceFeedback" class="fe-place-feedback" aria-live="polite" role="status" hidden></p>

                <div id="sariFacilityMap" role="group" aria-labelledby="facilityMapLabel"></div>
                <div id="facilityPinPreview" class="fe-pin-preview" hidden>
                    <span class="fe-pin-preview-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M20 10c0 6-8 12-8 12S4 16 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="2.5"/></svg></span>
                    <span id="facilityPinPreviewText">Confirm this facility location to continue.</span>
                </div>
                <button type="button" class="fe-confirm-location" id="facilityConfirmLocation" disabled>Confirm Hub Location</button>
                <p id="facilityLocationConfirmed" class="fe-confirmed-location" aria-live="polite" hidden></p>
                <p id="facilityMapError" class="fe-error" role="alert" hidden></p>

                {{-- Backend fields remain present and are set only after user confirmation. --}}
                <input id="sariFacilityLatitude" type="hidden" name="latitude" value="{{ old('latitude', $editing?->latitude) }}">
                <input id="sariFacilityLongitude" type="hidden" name="longitude" value="{{ old('longitude', $editing?->longitude) }}">
            </div>

            <label class="fe-active-row" for="facilityActive"><input id="facilityActive" name="is_active" type="checkbox" value="1" @checked(old('is_active', $editing?->is_active ?? true))><span><strong>Active for new routes</strong><small>Turn off to keep the hub in history without new assignments.</small></span></label>
        </div>

        <footer class="fe-form-footer"><span>Branch code is generated automatically when saved.</span><button id="hubSubmitButton" type="submit" class="fe-save">{{ $editing ? 'Save Hub Changes' : 'Create Sorting Hub' }}</button></footer>
    </form>
</section>

<dialog id="facilityHelpDialog" class="fe-help-dialog" aria-labelledby="facilityHelpTitle" aria-describedby="facilityHelpIntro">
    <div class="fe-help-heading"><div><p class="fe-kicker">02 · Branch Access</p><h3 id="facilityHelpTitle">How branches work</h3><p id="facilityHelpIntro">Branches use the same authenticated Logistics account. No separate branch email or password is required.</p></div><button type="button" id="facilityHelpClose" class="fe-help-close" aria-label="Close guide"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m5 5 14 14M19 5 5 19"/></svg></button></div>
    <div class="fe-help-body">
        <section><h4>How access works</h4><strong>Login once, then select the operating branch.</strong><p>Parcel scans, sorting, hub transfers, last-mile work, Rider COD intake, and branch treasury are scoped to the selected facility.</p></section>
        <section><h4>Branch code</h4><strong>{{ $editing?->code ?: 'Generated automatically after save' }}</strong><p>SARI creates a unique facility code. You do not need to enter one manually.</p></section>
        <section><h4>Main Branch rule</h4><strong>@if($editing?->is_main_branch) This facility is the current Main Branch. @elseif(!$editing && ($stats['main_branch_count'] ?? 0) === 0) The first active facility becomes Main Branch automatically. @else A regular branch can be promoted later from the hub details. @endif</strong><p>The Main Branch is the central COD treasury destination; parcel routing follows the origin and destination facilities.</p></section>
        <section><h4>What SARI needs</h4><ul><li>Facility name and complete physical address</li><li>Province, municipality, and optional barangay</li><li>Confirmed hub location selected on the map</li><li>Active / inactive routing status</li></ul><p>No operator name, operator email, or branch password is collected.</p></section>
    </div>
</dialog>
