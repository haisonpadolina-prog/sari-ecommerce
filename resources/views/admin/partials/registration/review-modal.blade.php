{{-- Registration review: presentation only. Existing protected routes handle decisions and files. --}}
<div id="reviewBackdrop" class="registration-modal-backdrop" aria-hidden="true"></div>
<aside id="reviewDrawer" class="registration-modal" role="dialog" aria-modal="true" aria-label="Registration application review" aria-hidden="true" tabindex="-1" inert>
    @foreach($applications as $application)
        @php
            $detailKey = (string) $application->getKey();
            $requiredDocuments = match($application->role) {
                'seller' => array_values(array_filter([
                    ['label' => 'Government-issued ID', 'type' => 'id', 'available' => (bool) $application->id_path],
                    ($application->seller_type === 'sole_proprietorship' || $application->business_permit_path) ? ['label' => 'DTI Certificate', 'type' => 'permit', 'available' => (bool) $application->business_permit_path] : null,
                    ($application->seller_type !== 'individual' || $application->bir_path) ? ['label' => 'BIR Form 2303', 'type' => 'bir', 'available' => (bool) $application->bir_path] : null,
                    ($application->seller_type === 'corporation' || $application->sec_path) ? ['label' => 'SEC Certificate', 'type' => 'sec', 'available' => (bool) $application->sec_path] : null,
                ])),
                'logistics' => [
                    ['label' => 'Authorized Representative ID', 'type' => 'id', 'available' => (bool) $application->id_path],
                    ['label' => 'Mayor / Business Permit', 'type' => 'permit', 'available' => (bool) $application->business_permit_path],
                    ['label' => 'BIR Form 2303', 'type' => 'bir', 'available' => (bool) $application->bir_path],
                    ['label' => 'SEC Certificate', 'type' => 'sec', 'available' => (bool) $application->sec_path],
                ],
                'courier', 'rider' => [
                    ['label' => 'ID / Driver’s License', 'type' => 'id', 'available' => (bool) $application->id_path],
                    ['label' => 'OR / CR', 'type' => 'orcr', 'available' => (bool) $application->orcr_path],
                ],
                default => [['label' => 'Valid ID', 'type' => 'id', 'available' => (bool) $application->id_path]],
            };
            $allDocumentsAvailable = collect($requiredDocuments)->every(fn ($document) => $document['available']);
            $profileUrl = $application->profile_image_path
                ? route('admin.registrations.document', [$application, 'profile'])
                : null;
            $initials = mb_strtoupper(mb_substr($application->first_name ?? '', 0, 1) . mb_substr($application->last_name ?? '', 0, 1));
            $baseInfo = [
                ['Email address', $application->email],
                ['Contact number', $application->contact_no],
                ['Sex', $application->sex],
                ['Age', $application->age ? $application->age . ' years old' : null],
                ['Birthday', $application->birthday?->format('M d, Y')],
                ['Submitted', $application->created_at?->format('M d, Y h:i A')],
            ];
        @endphp
        <div data-application-detail="{{ $detailKey }}" class="registration-modal-detail" hidden>
            <header class="registration-modal-header">
                <div>
                    <h2 id="registration-review-title-{{ $detailKey }}">Registration review</h2>
                    <p>Application #{{ $application->getKey() }} <span aria-hidden="true">·</span> {{ ucfirst($application->role) }}</p>
                </div>
                <button type="button" data-close-review class="registration-modal-close" aria-label="Close application review">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m6 6 12 12M18 6 6 18" /></svg>
                </button>
            </header>
            <nav class="registration-modal-tabs" aria-label="Review sections">
                <button type="button" class="is-active" data-detail-tab="overview" data-detail-owner="{{ $detailKey }}" aria-controls="registration-overview-{{ $detailKey }}" aria-pressed="true">Applicant details</button>
                @if(in_array($application->role, ['seller', 'logistics'], true))
                    <button type="button" data-detail-tab="business" data-detail-owner="{{ $detailKey }}" aria-controls="registration-business-{{ $detailKey }}" aria-pressed="false">Business details</button>
                @endif
                <button type="button" data-detail-tab="documents" data-detail-owner="{{ $detailKey }}" aria-controls="registration-documents-{{ $detailKey }}" aria-pressed="false">Documents</button>
                <button type="button" data-detail-tab="decision" data-detail-owner="{{ $detailKey }}" aria-controls="registration-decision-{{ $detailKey }}" aria-pressed="false">Decision</button>
            </nav>
            <div class="registration-modal-body">
                <section id="registration-overview-{{ $detailKey }}" data-detail-tab-panel="overview" data-detail-owner="{{ $detailKey }}" aria-label="Applicant details">
                    <div class="registration-modal-summary">
                        <div class="registration-modal-identity">
                            <span class="registration-modal-avatar">
                                <span aria-hidden="true">{{ $initials ?: 'SA' }}</span>
                                @if($profileUrl)
                                    <img data-registration-avatar-src="{{ $profileUrl }}" alt="Profile photo of {{ $application->fullName() }}" data-registration-avatar>
                                @endif
                            </span>
                            <div><p>{{ $application->fullName() }}</p><span class="registration-modal-identity-caption">{{ ucfirst($application->role) }} applicant</span></div>
                        </div>
                        <span class="registration-modal-status" data-status="{{ $application->status }}">{{ ucfirst($application->status) }}</span>
                    </div>
                    <dl class="registration-modal-fields">
                        @foreach($baseInfo as [$label, $value])
                            <div class="registration-modal-field"><dt>{{ $label }}</dt><dd>{{ $value ?: '—' }}</dd></div>
                        @endforeach
                    </dl>
                    <h3 class="registration-modal-section-title">Registered address</h3>
                    @php
                        $addressInfo = [
                            ['Region', $application->region_name],
                            ['Province', $application->province_name],
                            ['City / Municipality', $application->municipality_name],
                            ['Barangay', $application->barangay_name],
                            ['Street / House address', $application->street_address],
                            ['Postal code', $application->postal_code],
                        ];
                    @endphp
                    <dl class="registration-modal-fields registration-modal-address">
                        @foreach($addressInfo as [$label, $value])
                            <div class="registration-modal-field"><dt>{{ $label }}</dt><dd>{{ $value ?: '—' }}</dd></div>
                        @endforeach
                    </dl>
                    @if(in_array($application->role, ['courier', 'rider'], true))
                        <h3 class="registration-modal-section-title">Vehicle information</h3>
                        <dl class="registration-modal-fields">
                            <div class="registration-modal-field"><dt>Vehicle</dt><dd>{{ $application->vehicle_type ?: '—' }}</dd></div>
                            <div class="registration-modal-field"><dt>Plate number</dt><dd>{{ $application->plate_number ?: '—' }}</dd></div>
                        </dl>
                    @endif
                    @if($application->admin_note)
                        <h3 class="registration-modal-section-title">Recorded reviewer note</h3>
                        <p class="registration-modal-note">{{ $application->admin_note }}</p>
                    @endif
                </section>
                @if(in_array($application->role, ['seller', 'logistics'], true))
                    <section id="registration-business-{{ $detailKey }}" data-detail-tab-panel="business" data-detail-owner="{{ $detailKey }}" aria-label="Business details" hidden>
                        <h3 class="registration-modal-section-title registration-modal-section-title--first">Business registration</h3>
                        @php
                            $businessInfo = [
                                ['Business name', $application->business_name],
                                ['Tax identification number', $application->tin],
                                ['SEC registration number', $application->sec_registration_no],
                                ['Postal code', $application->postal_code],
                            ];
                            if ($application->role === 'seller') {
                                $businessInfo[] = ['Seller type', str_replace('_', ' ', ucfirst($application->seller_type ?? ''))];
                                $businessInfo[] = ['Line of business', $application->line_of_business];
                            } else {
                                $businessInfo[] = ['Representative position', $application->position];
                            }
                        @endphp
                        <dl class="registration-modal-fields">
                            @foreach($businessInfo as [$label, $value])
                                <div class="registration-modal-field"><dt>{{ $label }}</dt><dd>{{ $value ?: '—' }}</dd></div>
                            @endforeach
                            @if($application->role === 'logistics')
                                <div class="registration-modal-field registration-modal-field--wide"><dt>Service areas</dt><dd>{{ $application->service_areas ?: '—' }}</dd></div>
                            @endif
                        </dl>
                    </section>
                @endif
                <section id="registration-documents-{{ $detailKey }}" data-detail-tab-panel="documents" data-detail-owner="{{ $detailKey }}" aria-label="Verification documents" hidden>
                    <h3 class="registration-modal-section-title registration-modal-section-title--first">Verification documents</h3>
                    <p class="registration-modal-copy">Review the submitted files before making a decision.</p>
                    <div class="registration-modal-documents">
                        @foreach($requiredDocuments as $document)
                            @php
                                $path = match($document['type']) {
                                    'id' => $application->id_path,
                                    'permit' => $application->business_permit_path,
                                    'bir' => $application->bir_path,
                                    'sec' => $application->sec_path,
                                    'orcr' => $application->orcr_path,
                                    default => null,
                                };
                                $extension = strtolower(pathinfo((string) $path, PATHINFO_EXTENSION));
                                $documentUrl = $document['available'] ? route('admin.registrations.document', [$application, $document['type']]) : null;
                            @endphp
                            <article class="registration-modal-document">
                                <div class="registration-modal-document-heading"><h4>{{ $document['label'] }}</h4><span class="{{ $document['available'] ? 'registration-modal-file-status' : 'registration-modal-missing' }}">{{ $document['available'] ? 'Submitted' : 'Missing' }}</span></div>
                                @if($document['available'])
                                    @if(in_array($extension, ['jpg', 'jpeg', 'png', 'webp', 'gif'], true))
                                        <a href="{{ $documentUrl }}" target="_blank" rel="noopener noreferrer" class="registration-modal-preview" aria-label="Open {{ $document['label'] }} in a new tab"><img data-registration-preview-src="{{ $documentUrl }}" alt="{{ $document['label'] }} submitted by {{ $application->fullName() }}" loading="lazy"></a>
                                    @elseif($extension === 'pdf')
                                        <div class="registration-modal-preview registration-modal-preview--pdf"><span>PDF document</span><p>Open the original file to review all pages.</p></div>
                                    @else
                                        <div class="registration-modal-preview registration-modal-preview--pdf"><span>Verification file</span><p>Open the submitted file to review it.</p></div>
                                    @endif
                                    <a href="{{ $documentUrl }}" target="_blank" rel="noopener noreferrer" class="registration-modal-file-link">Open original file</a>
                                @else
                                    <div class="registration-modal-preview registration-modal-preview--empty">File not submitted</div>
                                @endif
                            </article>
                        @endforeach
                    </div>
                </section>
                <section id="registration-decision-{{ $detailKey }}" data-detail-tab-panel="decision" data-detail-owner="{{ $detailKey }}" aria-label="Registration decision" hidden>
                    @if($application->status === 'pending' && $application->role !== 'rider')
                        <h3 class="registration-modal-section-title registration-modal-section-title--first">Complete your review</h3>
                        <p class="registration-modal-copy">Choose a decision after reviewing the applicant details and documents.</p>
                        @if(!$allDocumentsAvailable)
                            <p class="registration-modal-warning">Some required documents are missing. Review the available files before deciding.</p>
                        @endif
                        <div class="registration-modal-decisions">
                            <form method="POST" action="{{ route('admin.registrations.approve', $application) }}">
                                @csrf
                                <input type="hidden" name="return_role" value="{{ $role ?? '' }}">
                                <div class="registration-decision-heading">
                                    <span class="registration-decision-symbol registration-decision-symbol--approve" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m6 12 4 4 8-8" /></svg></span>
                                    <div><h4>Approve registration</h4><p>Confirm the application and activate the account.</p></div>
                                </div>
                                <label for="approve-note-{{ $detailKey }}">Reviewer note <span>(optional)</span></label>
                                <textarea id="approve-note-{{ $detailKey }}" name="admin_note" maxlength="1500" rows="4" placeholder="Add a short approval note…"></textarea>
                                <button type="submit" class="registration-modal-button registration-modal-button--approve">Approve registration</button>
                            </form>
                            <form method="POST" action="{{ route('admin.registrations.reject', $application) }}">
                                @csrf
                                <input type="hidden" name="return_role" value="{{ $role ?? '' }}">
                                <div class="registration-decision-heading">
                                    <span class="registration-decision-symbol registration-decision-symbol--decline" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m7 7 10 10M17 7 7 17" /></svg></span>
                                    <div><h4>Decline registration</h4><p>Record a clear reason for the applicant.</p></div>
                                </div>
                                <label for="reject-note-{{ $detailKey }}">Rejection reason <span>(required)</span></label>
                                <textarea id="reject-note-{{ $detailKey }}" name="admin_note" required minlength="5" maxlength="1500" rows="4" placeholder="Explain what needs to be corrected…"></textarea>
                                <button type="submit" class="registration-modal-button registration-modal-button--decline">Decline registration</button>
                            </form>
                        </div>
                    @elseif($application->role === 'rider' && $application->status === 'pending')
                        <h3 class="registration-modal-section-title registration-modal-section-title--first">Reviewed by Logistics</h3>
                        <p class="registration-modal-copy">Rider applications are approved or declined by their selected Logistics provider. Applicant details and documents are available here for reference.</p>
                    @else
                        <h3 class="registration-modal-section-title registration-modal-section-title--first">Review completed</h3>
                        <p class="registration-modal-copy">This application has been {{ $application->status }}.</p>
                        @if($application->admin_note)<p class="registration-modal-note">{{ $application->admin_note }}</p>@endif
                    @endif
                </section>
            </div>
            <footer class="registration-modal-footer">
                <button type="button" data-close-review class="registration-modal-button registration-modal-button--secondary">Close</button>
                @if($application->status === 'pending' && $application->role !== 'rider')
                    <button type="button" class="registration-modal-button registration-modal-button--primary" data-review-action="accept" data-detail-owner="{{ $detailKey }}">Review decision</button>
                @else
                    <span class="registration-modal-footer-note">{{ $application->status === 'pending' ? 'Logistics review' : 'Review history' }}</span>
                @endif
            </footer>
        </div>
    @endforeach
</aside>
