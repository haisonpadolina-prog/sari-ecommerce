<style id="adminSellerShellStyles">
/* Based on the final rendered Seller shell in layouts/seller.blade.php. */
:root {
    --admin-sidebar-expanded: 238px;
    --admin-sidebar-collapsed: 76px;
    --admin-shell-gold: #D9950B;
}
#adminSidebar {
    width: var(--admin-sidebar-width, 238px) !important;
    overflow-x: hidden;
    border-right-color: #E4E7EC !important;
    background: #FFFFFF !important;
    box-shadow: none !important;
}
#adminSidebarBrand {
    min-height: 74px !important;
    padding-inline: 12px !important;
    border-bottom-color: #EAECF0 !important;
}
#adminSidebarLogo { height: 44px !important; }
#adminSidebarFullLogo {
    display: block !important;
    width: 102px !important;
    height: auto !important;
}
#adminSidebarCompactLogo {
    display: none !important;
    width: 38px !important;
    height: 38px !important;
}
#adminSidebarNav {
    padding: 14px 10px 16px !important;
    scrollbar-width: thin;
    scrollbar-color: #D0D5DD transparent;
}
#adminSidebarNav::-webkit-scrollbar { width: 4px; }
#adminSidebarNav::-webkit-scrollbar-thumb {
    border-radius: 999px;
    background: #D0D5DD !important;
}
.admin-sidebar-section-label {
    margin: 13px 9px 8px !important;
    color: #667085 !important;
    font-size: 9.5px !important;
    line-height: 1.3 !important;
    font-weight: 750 !important;
    letter-spacing: .12em !important;
    text-transform: uppercase !important;
}
.admin-sidebar-section-label:first-child { margin-top: 4px !important; }
.admin-sidebar-section-divider {
    height: 1px !important;
    margin: 11px 6px 9px !important;
    background: #EAECF0 !important;
}
#adminSidebarNav > a[data-sidebar-item],
#adminSidebarNav > button[data-sidebar-item],
#adminSidebarNav > form > button[data-sidebar-item] {
    width: 100% !important;
    min-height: 43px !important;
    gap: 11px !important;
    border: 1px solid transparent !important;
    border-radius: 10px !important;
    padding: 9px 11px !important;
    color: #475467 !important;
    font-size: 12.5px !important;
    line-height: 1.3 !important;
    font-weight: 600 !important;
    letter-spacing: -.008em !important;
    background-color: transparent !important;
    background-image: none !important;
    box-shadow: none !important;
    transform: none !important;
    transition: background-color .12s ease, border-color .12s ease,
                color .12s ease, box-shadow .12s ease !important;
}
#adminSidebarNav [data-sidebar-item] svg {
    width: 18px !important;
    height: 18px !important;
    flex: 0 0 18px;
    stroke-width: 1.8 !important;
}
#adminSidebarNav [data-sidebar-item] > span:first-child { gap: 11px !important; }
#adminSidebarNav > a[data-sidebar-item] + a[data-sidebar-item],
#adminSidebarNav > a[data-sidebar-item] + form,
#adminSidebarNav > form + a[data-sidebar-item] { margin-top: 2px !important; }
#adminSidebarNav > a[data-sidebar-item][data-admin-nav-active="true"],
#adminSidebarNav > a[data-sidebar-item][data-admin-nav-active="true"]:hover {
    border-color: #D9950B !important;
    background-color: #D9950B !important;
    color: #FFFFFF !important;
    font-weight: 600 !important;
    box-shadow: 0 5px 12px rgba(194, 132, 10, .12) !important;
}
#adminSidebarNav > a[data-sidebar-item][data-admin-nav-active="true"] svg,
#adminSidebarNav > a[data-sidebar-item][data-admin-nav-active="true"] .sidebar-label {
    color: #FFFFFF !important;
    stroke: currentColor !important;
}
#adminSidebarNav > a[data-sidebar-item]:not([data-admin-nav-active="true"]):hover {
    border-color: #E4E7EC !important;
    background-color: #F2F4F7 !important;
    color: #344054 !important;
    box-shadow: none !important;
}
/* Buyer-style red Logout; do not let the generic gold hover override it. */
#adminSidebarNav #adminLogoutForm > button[data-sidebar-item] {
    color: #EF4444 !important;
}
#adminSidebarNav #adminLogoutForm > button[data-sidebar-item]:hover,
#adminSidebarNav #adminLogoutForm > button[data-sidebar-item]:focus-visible {
    border-color: #FEE2E2 !important;
    background-color: #FEF2F2 !important;
    color: #DC2626 !important;
}
#adminSidebarMessageBadge,
#adminSidebarNotifications [data-admin-live-count] {
    min-width: 18px !important;
    height: 18px !important;
    padding-inline: 5px !important;
    font-size: 7.5px !important;
}
/* Buyer header hierarchy: menu + title/crumb | bell + account avatar. */
#adminHeader {
    border-bottom-color: #E9EDF2 !important;
    background: #FFFFFF !important;
    background-image: none !important;
    box-shadow: none !important;
}
#adminHeader .admin-header-main {
    min-height: 82px !important;
    gap: 16px !important;
    padding-inline: 16px !important;
}
#adminHeader .admin-header-main > div:first-child { gap: 10px !important; }
#adminHeader .admin-header-actions { gap: 6px !important; }
#adminHeader #adminMenuButton,
#adminHeader #adminSidebarToggle,
#adminHeader #adminLiveNotificationButton {
    width: 38px !important;
    height: 38px !important;
    flex: 0 0 38px;
    border-radius: 10px !important;
}
#adminHeader #adminMenuButton,
#adminHeader #adminSidebarToggle {
    border-color: #E5EAF0 !important;
    background-color: #FFFFFF !important;
    color: #344054 !important;
    box-shadow: none !important;
}
#adminHeader #adminMenuButton:hover,
#adminHeader #adminSidebarToggle:hover {
    border-color: #D4DBE4 !important;
    background-color: #F4F6F9 !important;
}
#adminHeader #adminMenuButton svg,
#adminHeader #adminSidebarToggle svg,
#adminHeader #adminLiveNotificationButton > svg {
    width: 17px !important;
    height: 17px !important;
}
#adminHeader h1 {
    color: #17212F !important;
    font-size: clamp(21px, 1.85vw, 27px) !important;
    line-height: 1.22 !important;
    font-weight: 700 !important;
    letter-spacing: -.035em !important;
}
#adminHeader .admin-header-breadcrumb {
    display: flex !important;
    gap: 8px !important;
    margin-top: 4px !important;
    color: #758092 !important;
    font-size: 11.5px !important;
    line-height: 1.4 !important;
}
#adminHeader .admin-header-breadcrumb svg {
    width: 13px !important;
    height: 13px !important;
    flex: 0 0 13px !important;
    color: #B5BDC8 !important;
}
#adminHeader .admin-header-profile {
    display: flex !important;
    gap: 11px !important;
    border-radius: 13px !important;
    padding: 6px 9px !important;
    color: #202B3A !important;
    text-decoration: none !important;
}
#adminHeader .admin-header-profile:hover {
    border-color: #E5EAF0 !important;
    background-color: #F6F8FB !important;
}
#adminHeader #adminProfileButton > div:first-child {
    width: 39px !important;
    height: 39px !important;
    flex: 0 0 39px !important;
    font-size: 12px !important;
    box-shadow: 0 0 0 2px #FFFFFF, 0 0 0 3px #EAECF0 !important;
}
#adminHeader .admin-header-profile-details p:first-child {
    font-size: 12px !important;
    line-height: 1.3 !important;
    font-weight: 650 !important;
}
#adminHeader .admin-header-profile-details p:last-child {
    font-size: 10px !important;
    line-height: 1.4 !important;
}
#adminHeader #adminLiveNotificationButton [data-admin-live-count] {
    right: -5px !important;
    top: -5px !important;
    min-width: 18px !important;
    height: 18px !important;
    font-size: 8px !important;
}
@media (min-width: 640px) {
    #adminHeader .admin-header-main { padding-inline: 24px !important; }
    #adminHeader .admin-header-actions { gap: 8px !important; }
}
@media (min-width: 1024px) {
    #adminHeader .admin-header-main { padding-inline: 20px !important; }
    html.admin-sidebar-collapsed #adminSidebarBrand {
        min-height: 74px !important;
        padding-inline: 8px !important;
    }
    html.admin-sidebar-collapsed #adminSidebarFullLogo { display: none !important; }
    html.admin-sidebar-collapsed #adminSidebarCompactLogo { display: block !important; }
    html.admin-sidebar-collapsed #adminSidebarNav { padding: 12px 9px !important; }
    html.admin-sidebar-collapsed #adminSidebarNav [data-sidebar-item] {
        min-height: 43px !important;
        gap: 0 !important;
        padding-inline: 0 !important;
        justify-content: center !important;
    }
    html.admin-sidebar-collapsed #adminSidebarProfile { padding: 9px !important; }
    html.admin-sidebar-collapsed #adminSidebarProfile > a {
        justify-content: center !important;
        padding: 8px !important;
    }
}
@media (min-width: 1280px) {
    #adminHeader .admin-header-main { padding-inline: 24px !important; }
}
@media (max-width: 1279px) {
    #adminHeader .admin-header-profile { padding: 5px !important; }
}
@media (max-height: 820px) and (min-width: 1024px) {
    #adminSidebarNav { padding-top: 10px !important; padding-bottom: 10px !important; }
    .admin-sidebar-section-label { margin-top: 8px !important; margin-bottom: 6px !important; }
    .admin-sidebar-section-divider { margin-top: 8px !important; margin-bottom: 7px !important; }
    #adminSidebarNav > a[data-sidebar-item],
#adminSidebarNav > button[data-sidebar-item],
    #adminSidebarNav > form > button[data-sidebar-item] {
        min-height: 40px !important;
        padding-top: 7px !important;
        padding-bottom: 7px !important;
        font-size: 12px !important;
    }
    html.admin-sidebar-collapsed #adminSidebarNav [data-sidebar-item] { min-height: 40px !important; }
}
@media (max-width: 1023px) {
    #adminSidebar { width: 238px !important; }
    #adminContent { padding-left: 0 !important; }
}
@media (max-width: 767px) {
    #adminHeader .admin-header-main {
        min-height: 74px !important;
        padding-inline: 12px !important;
        gap: 10px !important;
    }
    #adminHeader h1 { font-size: 21px !important; }
    #adminHeader .admin-header-breadcrumb { font-size: 10px !important; }
    #adminHeader #adminMenuButton,
    #adminHeader #adminLiveNotificationButton {
        width: 40px !important;
        height: 40px !important;
        flex-basis: 40px !important;
    }
    #adminHeader #adminProfileButton > div:first-child {
        width: 35px !important;
        height: 35px !important;
        flex-basis: 35px !important;
    }
}
@media (max-width: 380px) {
    #adminHeader .admin-header-main { gap: 5px !important; }
    #adminHeader #adminLiveNotificationButton { width: 34px !important; flex-basis: 34px !important; }
}
@media (prefers-reduced-motion: reduce) {
    #adminSidebar, #adminContent, #adminHeader .admin-header-profile {
        transition-duration: 1ms !important;
    }
}

/* Admin controls retain their IDs and behavior; only their presentation changes. */
#adminHeader { backdrop-filter:none!important; }
#adminHeader .admin-header-main { width:100%; }
#adminHeader .admin-header-actions { flex-shrink:0; }
#adminHeader #adminSearchWrapper { display:none!important; }
#adminHeader #adminMobileSearchPanel:not(.hidden) { display:block!important; border-color:#EAECF0!important; background:#fff!important; }
#adminHeader #adminLiveNotificationButton { border:0!important; background:transparent!important; color:#475467!important; box-shadow:none!important; }
#adminHeader #adminLiveNotificationButton:hover { color:#C9890B!important; }
#adminHeader #adminLiveNotificationButton [data-admin-live-count] { border:2px solid #fff; }
#adminHeader #adminProfileWrapper { display:block!important; }
#adminHeader .admin-header-profile-details { max-width:165px; }
#adminHeader #adminProfileButton > div:first-child { flex-shrink:0; border:1px solid #E4E7EC!important; border-radius:50%; background:#fff!important; overflow:hidden; }
#adminHeader .admin-header-brand-avatar { display:block; width:100%; height:100%; padding:0; border-radius:50%; object-fit:cover; }
#adminHeader #adminProfileButton > svg { display:none!important; }
#adminHeader #adminProfileButton > div:nth-child(2) p { overflow:hidden; text-overflow:ellipsis; }
#adminHeader #adminProfilePanel { width:280px!important; max-width:calc(100vw - 24px); border:1px solid #E4E7EC!important; border-radius:14px!important; background:#fff!important; box-shadow:0 18px 48px rgba(16,24,40,.13),0 3px 10px rgba(16,24,40,.04)!important; }
#adminHeader #adminProfilePanel > div:first-child { border-color:#EAECF0!important; background:#F9FAFB!important; }
#adminHeader #adminProfilePanel > div:first-child > div > div:first-child { border:1px solid #E4E7EC; background:#fff!important; overflow:hidden; }
#adminHeader #adminProfilePanel a, #adminHeader #adminProfilePanel button[type=submit] { min-height:42px; font-size:12px!important; border-radius:10px!important; }
#adminHeader #adminProfilePanel a:hover { background:#F2F4F7!important; color:#344054!important; }
#adminHeader #adminProfilePanel p:first-child { font-size:12px!important; color:#1D2939!important; }
#adminHeader #adminProfilePanel p:last-child { font-size:11px!important; color:#667085!important; }
#adminSidebarNav > a[data-sidebar-item][data-sidebar-current="true"]:not([data-admin-nav-active]) { border-color:#D9950B!important; background-color:#D9950B!important; color:#fff!important; box-shadow:0 5px 12px rgba(194,132,10,.12)!important; }
#adminSidebarNav > a[data-admin-nav-active="true"] svg, #adminSidebarNav > a[data-admin-nav-active="true"] .sidebar-label { color:#fff!important; }
#adminSidebarNav > form > button[data-sidebar-item] { color:#EF4444!important; }
#adminSidebarNav > form > button[data-sidebar-item]:hover { color:#DC2626!important; background:#FEF2F2!important; border-color:#FEE2E2!important; }
#adminSidebar #adminSidebarNav > button[data-sidebar-item] { display:flex; align-items:center; font-weight:600; text-align:left; }
#adminSidebar #adminSidebarNav > button[data-sidebar-item]:hover { background:#F2F4F7!important; }
#adminSidebarNav > button[data-sidebar-item] [data-admin-live-count] { margin-left:auto; }
#adminSidebarNav { scrollbar-width:none!important; }
#adminSidebarNav::-webkit-scrollbar { display:none; }
#adminSidebarClose:hover { background:#F2F4F7!important; color:#344054!important; }
#adminSidebar .admin-sidebar-section-label:first-child { margin-top:4px!important; }
#adminSidebar { max-width:86vw; }
@media(min-width:1024px) {
    html.admin-sidebar-collapsed #adminSidebarNotifications { position:relative; }
    html.admin-sidebar-collapsed #adminSidebarNotifications [data-admin-live-count] { position:absolute; top:0; right:0; width:auto!important; min-width:16px!important; max-width:none!important; height:16px; opacity:1!important; padding-inline:3px!important; }
}
@media(max-width:1279px) { #adminHeader .admin-header-profile-details { display:none!important; } }
@media(min-width:1280px) { #adminHeader .admin-header-profile-details { display:block!important; } }
@media(max-width:380px) { #adminHeader #adminMenuButton { width:34px!important; flex-basis:34px!important; } #adminHeader h1 { font-size:19px!important; } }
@media(prefers-reduced-motion:reduce) { #adminHeader *, #adminSidebar * { transition:none!important; } }


#adminHeader #adminProfileButton > div:nth-child(2) p:first-child { font-size:12px!important; line-height:1.3!important; font-weight:650!important; color:#202B3A!important; }
#adminHeader #adminProfileButton > div:nth-child(2) p:last-child { font-size:10px!important; line-height:1.4!important; color:#667085!important; }
@media(max-height:820px) and (min-width:1024px) { #adminSidebar #adminSidebarNav > button[data-sidebar-item] { min-height:40px!important; padding-block:7px!important; font-size:12px!important; } }

/* Neutral notification panel: no colored edge or cream accents. */
#adminHeader #adminLiveNotificationPanel { border:1px solid #E4E7EC!important; border-radius:16px!important; background:#fff!important; box-shadow:0 16px 48px rgba(16,24,40,.12),0 3px 10px rgba(16,24,40,.04)!important; }
#adminHeader .admin-live-notification-heading { padding:17px 18px!important; background:#fff!important; }
#adminHeader .admin-live-notification-heading h2 { font-size:14px!important; line-height:1.4!important; color:#1D2939!important; }
#adminHeader .admin-live-notification-heading p { font-size:11px!important; color:#667085!important; }
#adminHeader .admin-live-notification-tools { padding:10px 18px!important; background:#F8FAFC!important; border-color:#EAECF0!important; }
#adminHeader #adminNotificationSound { border-color:#E4E7EC!important; border-radius:9px!important; background:#fff!important; color:#475467!important; }
#adminHeader #adminNotificationSound:hover { background:#F2F4F7!important; }
#adminHeader .admin-live-notification-row,
#adminHeader .admin-live-notification-row[data-unread=true] { padding:14px 18px!important; border-left:0!important; border-right:0!important; border-bottom:1px solid #EEF1F5!important; background:#fff!important; box-shadow:none!important; }
#adminHeader .admin-live-notification-row[data-unread=true] { background:#F8FAFC!important; }
#adminHeader .admin-live-notification-row:hover { background:#F2F4F7!important; }
#adminHeader .admin-live-notification-row strong { font-size:12px!important; font-weight:600!important; color:#344054!important; }
#adminHeader .admin-live-notification-row[data-unread=true] strong::after { content:''; display:inline-block; width:6px; height:6px; margin-left:7px; border-radius:50%; background:#64748B; vertical-align:middle; }
#adminHeader .admin-live-notification-row p { margin:4px 0 6px!important; font-size:11px!important; line-height:1.55!important; color:#475467!important; }
#adminHeader .admin-live-notification-row time { font-size:10px!important; color:#98A2B3!important; }
#adminHeader .admin-live-notification-row:last-child { border-bottom:0!important; }
#adminLiveNotificationButton [data-admin-live-count], #adminSidebarNotifications [data-admin-live-count] { background:#475467!important; }
/* Crop the original rounded-square mark into the circular avatar. */
#adminHeader #adminProfileButton > div:first-child,
#adminHeader #adminProfilePanel > div:first-child > div > div:first-child { display:grid!important; place-items:center!important; padding:0!important; border:0!important; border-radius:50%!important; background:#101112!important; box-shadow:none!important; overflow:hidden!important; isolation:isolate; }
#adminHeader .admin-header-brand-avatar { display:block!important; width:100%!important; height:100%!important; max-width:none!important; padding:0!important; margin:0!important; border:0!important; border-radius:0!important; object-fit:cover!important; transform:scale(1.22)!important; transform-origin:center; }
</style>
