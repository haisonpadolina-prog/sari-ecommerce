{{-- Registration review: presentation only. Existing protected routes handle decisions and files. --}}
<div id="reviewBackdrop" class="registration-modal-backdrop" aria-hidden="true"></div>
<aside id="reviewDrawer" class="registration-modal" role="dialog" aria-modal="true" aria-label="Registration application review" aria-hidden="true" tabindex="-1" inert>
    @foreach($applications as $application)
        @php
            $detailKey = (string) $application->getKey();
            $requiredDocuments = match($application->role) {
                'seller' => array_values(array_filter([
                    ['label' => 'Government-issued ID', 'type' => 'id', 'available' => (bool) $application->id_path],
                    $application->seller_type === 'sole_proprietorship' ? ['label' => 'DTI Certificate', 'type' => 'permit', 'available' => (bool) $application->business_permit_path] : null,
                    $application->seller_type !== 'individual' ? ['label' => 'BIR Form 2303', 'type' => 'bir', 'available' => (bool) $application->bir_path] : null,
                    $application->seller_type === 'corporation' ? ['label' => 'SEC Certificate', 'type' => 'sec', 'available' => (bool) $application->sec_path] : null,
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
            $baseInfo = [
                ['Full name', $application->fullName()],
                ['Account type', ucfirst($application->role)],
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
                <button type="button" data-detail-tab="documents" data-detail-owner="{{ $detailKey }}" aria-controls="registration-documents-{{ $detailKey }}" aria-pressed="false">Documents</button>
                <button type="button" data-detail-tab="decision" data-detail-owner="{{ $detailKey }}" aria-controls="registration-decision-{{ $detailKey }}" aria-pressed="false">Decision</button>
            </nav>
            <div class="registration-modal-body">
                <section id="registration-overview-{{ $detailKey }}" data-detail-tab-panel="overview" data-detail-owner="{{ $detailKey }}" aria-label="Applicant details">
                    <div class="registration-modal-summary">
                        <p>{{ $application->fullName() }}</p>
                        <span class="registration-modal-status" data-status="{{ $application->status }}">{{ ucfirst($application->status) }}</span>
                    </div>
                    <dl class="registration-modal-fields">
                        @foreach($baseInfo as [$label, $value])
                            <div class="registration-modal-field"><dt>{{ $label }}</dt><dd>{{ $value ?: '—' }}</dd></div>
                        @endforeach
                        <div class="registration-modal-field registration-modal-field--wide"><dt>Registered address</dt><dd>{{ collect([$application->street_address, $application->barangay_name, $application->municipality_name, $application->province_name])->filter()->implode(', ') ?: '—' }}</dd></div>
                    </dl>
                    @if(in_array($application->role, ['seller', 'logistics'], true))
                        <h3 class="registration-modal-section-title">Business information</h3>
                        <dl class="registration-modal-fields">
                            <div class="registration-modal-field"><dt>{{ $application->role === 'logistics' ? 'Logistics / sorting center' : 'Business name' }}</dt><dd>{{ $application->business_name ?: '—' }}</dd></div>
                            @if($application->role === 'seller')
                                <div class="registration-modal-field"><dt>Line of business</dt><dd>{{ $application->line_of_business ?: '—' }}</dd></div>
                            @else
                                <div class="registration-modal-field"><dt>Position</dt><dd>{{ $application->position ?: '—' }}</dd></div>
                                <div class="registration-modal-field registration-modal-field--wide"><dt>Service areas</dt><dd>{{ $application->service_areas ?: '—' }}</dd></div>
                            @endif
                        </dl>
                    @endif
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
                <section id="registration-documents-{{ $detailKey }}" data-detail-tab-panel="documents" data-detail-owner="{{ $detailKey }}" aria-label="Verification documents" hidden>
                    <h3 class="registration-modal-section-title registration-modal-section-title--first">Verification documents</h3>
                    <p class="registration-modal-copy">Review the submitted files before making a decision.</p>
                    <div class="registration-modal-documents">
                        @foreach($requiredDocuments as $document)
                            <article class="registration-modal-document">
                                <div><h4>{{ $document['label'] }}</h4><p>{{ $document['available'] ? 'Submitted · Protected file' : 'Not submitted' }}</p></div>
                                @if($document['available'])
                                    <a href="{{ route('admin.registrations.document', [$application, $document['type']]) }}" target="_blank" rel="noopener noreferrer" class="registration-modal-button registration-modal-button--secondary" aria-label="Open {{ $document['label'] }} in a new tab">View file <span aria-hidden="true">↗</span></a>
                                @else
                                    <span class="registration-modal-missing">Missing</span>
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
                                <h4>Approve registration</h4>
                                <label for="approve-note-{{ $detailKey }}">Reviewer note <span>(optional)</span></label>
                                <textarea id="approve-note-{{ $detailKey }}" name="admin_note" maxlength="1500" rows="4" placeholder="Add a short approval note…"></textarea>
                                <button type="submit" class="registration-modal-button registration-modal-button--approve">Approve registration</button>
                            </form>
                            <form method="POST" action="{{ route('admin.registrations.reject', $application) }}">
                                @csrf
                                <input type="hidden" name="return_role" value="{{ $role ?? '' }}">
                                <h4>Decline registration</h4>
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
                    <button type="button" class="registration-modal-button registration-modal-button--primary" data-review-action="accept" data-detail-owner="{{ $detailKey }}">Review decision <span aria-hidden="true">→</span></button>
                @else
                    <span class="registration-modal-footer-note">{{ $application->status === 'pending' ? 'Logistics review' : 'Review history' }}</span>
                @endif
            </footer>
        </div>
    @endforeach
</aside>
