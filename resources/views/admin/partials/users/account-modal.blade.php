{{-- CENTERED ENTERPRISE ACCOUNT MODAL --}}
<div
    id="profileBackdrop"
    class="fixed inset-0 z-[80] bg-[#17120d]/20"
    aria-hidden="true"
></div>

<aside
    id="profileDrawer"
    class="fixed z-[90] overflow-hidden bg-white"
    role="dialog"
    aria-modal="true"
    aria-labelledby="drawerName"
    aria-hidden="true"
>
    <div class="enterprise-user-modal flex h-full min-h-0 flex-col">
        {{-- MODAL HEADER --}}
        <header class="enterprise-user-modal-header">
            <div class="flex min-w-0 items-center gap-3">
                <span class="enterprise-user-modal-header-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <circle cx="9" cy="8" r="3"></circle>
                        <path d="M3 20a6 6 0 0 1 12 0"></path>
                        <path d="M17 11h4"></path>
                        <path d="M19 9v4"></path>
                    </svg>
                </span>

                <div class="min-w-0">
                    <p class="enterprise-user-modal-eyebrow">Account Management</p>
                    <div class="mt-1 flex min-w-0 flex-wrap items-center gap-x-2 gap-y-1">
                        <h3 id="drawerName" class="enterprise-user-modal-title truncate">User</h3>
                        <span class="enterprise-user-modal-context">Profile, activity & access</span>
                    </div>
                </div>
            </div>

            <button
                id="closeProfile"
                type="button"
                class="enterprise-modal-close"
                aria-label="Close account management modal"
            >
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true">
                    <path d="m7 7 10 10"></path>
                    <path d="m17 7-10 10"></path>
                </svg>
            </button>
        </header>

        {{-- MODAL NAVIGATION --}}
        <nav class="enterprise-user-modal-tabs" role="tablist" aria-label="Account management sections">
            <button type="button" class="drawer-tab is-active" data-drawer-tab="overview" id="account-tab-overview" role="tab" aria-controls="account-panel-overview" aria-selected="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <circle cx="12" cy="8" r="3.5"></circle>
                    <path d="M5 20a7 7 0 0 1 14 0"></path>
                </svg>
                <span>Overview</span>
            </button>

            <button type="button" class="drawer-tab" data-drawer-tab="activity" id="account-tab-activity" role="tab" aria-controls="account-panel-activity" aria-selected="false">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <circle cx="12" cy="12" r="8"></circle>
                    <path d="M12 7v5l3 2"></path>
                </svg>
                <span>Recent Activity</span>
            </button>

            <button type="button" class="drawer-tab" data-drawer-tab="manage" id="account-tab-manage" role="tab" aria-controls="account-panel-manage" aria-selected="false">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <path d="M4 20h4L19 9l-4-4L4 16v4Z"></path>
                    <path d="m13.5 6.5 4 4"></path>
                </svg>
                <span>Manage Account</span>
            </button>
        </nav>

        {{-- MODAL CONTENT --}}
        <div class="enterprise-user-modal-scroll flex-1 overflow-y-auto">
            {{-- OVERVIEW --}}
            <div data-drawer-panel="overview" id="account-panel-overview" role="tabpanel" aria-labelledby="account-tab-overview" class="enterprise-user-panel">
                <section class="account-review-summary">
                    <div class="account-review-identity">
                        <div class="user-photo-block">
                            <span id="drawerInitials" class="enterprise-profile-avatar">U</span>
                            <span id="drawerPhotoState" role="status" class="user-photo-state"></span>
                        </div>
                        <div class="account-review-identity-text">
                            <h4 id="drawerProfileName">User</h4>
                            <span id="drawerRole" class="enterprise-role-badge">User</span>
                        </div>
                        <span id="drawerStatusBadge" class="enterprise-status-badge">Active</span>
                    </div>
                    <div class="account-review-meta">
                        <span id="drawerId">—</span>
                        <span id="drawerAccess">—</span>
                        <span id="drawerJoined">Joined —</span>
                    </div>
                    <div class="account-review-contacts">
                        <div><p class="enterprise-detail-label">Email address</p><p id="drawerEmail" class="account-review-value">—</p></div>
                        <div><p class="enterprise-detail-label">Contact number</p><p id="drawerContact" class="account-review-value">—</p></div>
                    </div>
                </section>

                <section class="enterprise-card enterprise-full-profile-card mt-4">
                    <div id="drawerProfileSections" class="enterprise-profile-sections" aria-live="polite"></div>

                    <div id="drawerDocumentsWrap" class="enterprise-documents-wrap hidden">
                        <div class="enterprise-detail-section-heading">
                            <div>
                                <p>Registration Documents</p>
                                <span>Private documents are available only through the authenticated Admin session.</span>
                            </div>
                        </div>
                        <div id="drawerDocuments" class="enterprise-document-grid"></div>
                    </div>
                </section>
            </div>

            {{-- ACTIVITY --}}
            <div data-drawer-panel="activity" id="account-panel-activity" role="tabpanel" aria-labelledby="account-tab-activity" hidden class="enterprise-user-panel">
                <section class="account-activity-section">
                    <div class="enterprise-section-heading enterprise-section-heading--with-icon">
                        <div>
                            
                            <h4>Recent account activity</h4>
                            <p class="enterprise-section-copy">Account creation, notes and administrative actions.</p>
                        </div>

                        <span class="enterprise-section-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="M4 12h4l2-5 4 10 2-5h4"></path>
                            </svg>
                        </span>
                    </div>

                    <div id="activityTimeline" class="enterprise-activity-timeline"></div>
                </section>
            </div>

            {{-- MANAGE ACCOUNT --}}
            <div data-drawer-panel="manage" id="account-panel-manage" role="tabpanel" aria-labelledby="account-tab-manage" hidden class="enterprise-user-panel">
                <div class="enterprise-manage-grid">
                    <div class="space-y-3">

                        <a id="sellerMessageLink" href="#" class="hidden enterprise-card enterprise-message-card items-center justify-between">
                            <span class="flex min-w-0 items-center gap-3">
                                <span class="enterprise-message-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <path d="M4 5h16v12H8l-4 3V5Z"></path>
                                        <path d="M8 9h8"></path>
                                        <path d="M8 13h5"></path>
                                    </svg>
                                </span>
                                <span class="min-w-0">
                                    <strong>Seller messaging</strong>
                                    <small>Open the existing admin-to-seller conversation</small>
                                </span>
                            </span>

                            <svg viewBox="0 0 24 24" class="enterprise-message-arrow" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <path d="m9 18 6-6-6-6"></path>
                            </svg>
                        </a>
                    </div>

                    <div class="space-y-3">
                        <form id="noteForm" method="POST" class="enterprise-card enterprise-form-card">
                            @csrf

                            <div class="enterprise-section-heading">
                                <div>
                                    
                                    <h4>Admin note</h4>
                                    <p class="enterprise-section-copy">Private notes for administrators.</p>
                                </div>
                            </div>

                            <label for="adminNoteInput" class="enterprise-field-label mt-4">Internal note</label>
                            <textarea
                                id="adminNoteInput"
                                name="note"
                                required
                                minlength="2"
                                maxlength="1000"
                                rows="3"
                                placeholder="Write an internal note..."
                                class="clean-control enterprise-field-textarea"
                            ></textarea>

                            <button type="submit" class="enterprise-button enterprise-button--secondary mt-3 w-full">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true">
                                    <path d="M6 3h12v18H6z"></path>
                                    <path d="M9 8h6"></path>
                                    <path d="M9 12h6"></path>
                                </svg>
                                Add note
                            </button>
                        </form>

                        <section class="enterprise-card enterprise-access-card">
                            <div class="flex items-start gap-3">
                                <span class="enterprise-access-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <path d="M12 3v10"></path>
                                        <path d="M8 9l4 4 4-4"></path>
                                        <path d="M5 21h14"></path>
                                    </svg>
                                </span>

                                <div>
                                    
                                    <h4>Account access</h4>
                                    <p class="enterprise-section-copy">Manage sign-in access and account restrictions.</p>
                                </div>
                            </div>

                            <form id="suspendForm" method="POST" class="account-access-action">
                                <h5>Temporary suspension</h5>
                                <p class="enterprise-section-copy">Pause access until the account is restored.</p>
                                @csrf
                                <label for="suspendReasonInput" class="enterprise-field-label">Suspension reason</label>
                                <textarea
                                    id="suspendReasonInput"
                                    name="reason"
                                    required
                                    minlength="5"
                                    maxlength="500"
                                    rows="2"
                                    placeholder="Reason for suspension..."
                                    class="clean-control enterprise-field-textarea enterprise-field-textarea--danger"
                                ></textarea>

                                <button type="submit" class="enterprise-button enterprise-button--danger mt-3 w-full">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true">
                                        <circle cx="12" cy="12" r="8"></circle>
                                        <path d="m6.5 6.5 11 11"></path>
                                    </svg>
                                    Suspend user access
                                </button>
                            </form>

                            <form id="restoreForm" method="POST" class="mt-3 hidden">
                                @csrf
                                <button id="restoreAccessButton" type="submit" class="enterprise-button enterprise-button--success w-full">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true">
                                        <path d="M20 7v5h-5"></path>
                                        <path d="M18 16a7 7 0 1 1 1-8l1 4"></path>
                                    </svg>
                                    Restore user access
                                </button>
                            </form>

                            <form id="banForm" method="POST" class="account-access-action">
                                <h5>Ban account and email</h5>
                                <p class="enterprise-section-copy">Block login and registration with this email.</p>
                                @csrf
                                <label for="banReasonInput" class="enterprise-field-label">Ban reason</label>
                                <textarea id="banReasonInput" name="reason" required minlength="5" maxlength="500" rows="2" placeholder="Reason for banning this email..." class="clean-control enterprise-field-textarea enterprise-field-textarea--danger"></textarea>
                                <button id="banAccessButton" type="submit" class="enterprise-button enterprise-button--danger mt-3 w-full">Ban account and email</button>
                            </form>

                            <form id="unbanForm" method="POST" class="mt-4 hidden border-t border-[#eee8df] pt-4">
                                @csrf
                                <button id="unbanAccessButton" type="submit" class="enterprise-button enterprise-button--success w-full">Cancel ban & allow re-registration</button>
                            </form>

                            <form id="deleteUserForm" method="POST" class="account-delete-action" onsubmit="return confirm('Delete this account? The account will be removed and its email will be allowed to register again. Historical platform records will remain intact.');">
                                @csrf
                                @method('DELETE')
                                <div><h5>Delete account</h5><p class="enterprise-section-copy">Removes this account and releases its email for registration.</p></div>
                                <button id="deleteAccountButton" type="submit" class="enterprise-button enterprise-button--danger mt-3 w-full">Delete account</button>
                            </form>
                        </section>
                    </div>
                </div>
            </div>
        </div>
        <footer class="user-modal-footer"><button type="button" data-close-user-modal class="enterprise-button enterprise-button--secondary">Close</button><button type="button" data-jump-manage class="enterprise-button enterprise-button--primary">Manage account</button></footer>
    </div>
</aside>

