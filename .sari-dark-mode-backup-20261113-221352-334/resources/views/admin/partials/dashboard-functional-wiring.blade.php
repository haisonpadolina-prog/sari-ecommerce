{{--
    Functional wiring for the original SARI Admin Dashboard UI.
    This partial intentionally changes data and behavior only; it does not replace the dashboard design.
--}}
<script>
(function () {
    const liveData = @json($adminDashboardLive ?? []);

    function number(value) {
        return new Intl.NumberFormat('en-PH').format(Number(value || 0));
    }

    function currency(value, compact = false) {
        const amount = Number(value || 0);

        if (compact && Math.abs(amount) >= 1000) {
            return '₱' + new Intl.NumberFormat('en-PH', {
                notation: 'compact',
                maximumFractionDigits: 1,
            }).format(amount);
        }

        return '₱' + new Intl.NumberFormat('en-PH', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 2,
        }).format(amount);
    }

    function percent(value, signed = false) {
        const numeric = Number(value || 0);
        const prefix = signed && numeric > 0 ? '+' : '';
        return prefix + numeric.toFixed(1).replace(/\.0$/, '') + '%';
    }

    function findPanelByTitle(title) {
        return Array.from(document.querySelectorAll('.admin-panel, .admin-control-panel'))
            .find(function (panel) {
                return Array.from(panel.querySelectorAll('h3,h4'))
                    .some(function (heading) {
                        return heading.textContent.trim() === title;
                    });
            });
    }

    function setSummaryKpis() {
        const map = {
            'Total Users': {
                value: number(liveData.kpis?.total_users),
                trend: 'Live',
                note: 'approved accounts',
            },
            'Pending Registrations': {
                value: number(liveData.kpis?.pending_registrations),
                trend: number(liveData.kpis?.pending_registrations) + ' pending',
                note: 'awaiting review',
            },
            'Active Sellers': {
                value: number(liveData.kpis?.active_sellers),
                trend: 'Active',
                note: 'approved sellers',
            },
            'Total Orders': {
                value: number(liveData.kpis?.total_orders),
                trend: percent(liveData.sales?.orders_growth, true),
                note: 'orders this month trend',
            },
            'Open Complaints': {
                value: number(liveData.kpis?.open_complaints),
                trend: liveData.kpis?.open_complaints ? 'Needs review' : 'Clear',
                note: 'support queue',
            },
            'Platform Commission': {
                value: currency(liveData.kpis?.platform_commission, true),
                trend: percent(liveData.kpis?.commission_rate),
                note: 'current platform rate',
            },
        };

        document.querySelectorAll('.admin-kpi').forEach(function (card) {
            const paragraphs = card.querySelectorAll('p');
            const label = paragraphs[0]?.textContent.trim();
            const data = map[label];
            if (!data) return;

            if (paragraphs[1]) paragraphs[1].textContent = data.value;

            const footer = card.querySelector('.mt-3');
            const spans = footer?.querySelectorAll('span') || [];
            if (spans[0]) spans[0].textContent = data.trend;
            if (spans[1]) spans[1].textContent = data.note;
        });
    }

    function setSystemCard() {
        const labels = Array.from(document.querySelectorAll('p'));
        const weather = labels.find(function (node) {
            return node.textContent.trim() === 'Weather';
        });

        if (!weather) return;
        weather.textContent = 'Data';
        const wrapper = weather.parentElement;
        const paragraphs = wrapper?.querySelectorAll('p') || [];
        if (paragraphs[1]) paragraphs[1].textContent = 'Live';
        if (paragraphs[2]) paragraphs[2].textContent = 'Database';
    }

    function setHealth() {
        const health = liveData.health || {};
        const panel = document.getElementById('adminMarketplaceHealth');
        if (!panel) return;

        const score = Math.max(0, Math.min(100, Number(health.score || 0)));
        const ring = panel.querySelector('.admin-health-score');
        const scoreNode = ring?.querySelector('p');
        if (scoreNode) scoreNode.textContent = Math.round(score);
        if (ring) {
            const degrees = Math.round(score * 3.6);
            ring.style.background = `conic-gradient(#c99524 0deg ${degrees}deg, #eee6d9 ${degrees}deg 360deg)`;
        }

        const stableBadge = Array.from(panel.querySelectorAll('span')).find(function (node) {
            return ['Stable', 'Healthy'].includes(node.textContent.trim());
        });
        if (stableBadge && stableBadge.textContent.trim() === 'Stable') {
            stableBadge.textContent = health.risk === 'High' ? 'Needs attention' : (health.risk === 'Moderate' ? 'Watch' : 'Stable');
        }

        const grade = panel.querySelector('.admin-health-grade');
        if (grade) {
            grade.lastChild.textContent = score >= 85 ? ' Healthy' : (score >= 70 ? ' Stable' : ' Needs review');
        }

        const metrics = [
            ['Order Success Rate', health.order_success, score >= 80 ? 'Strong' : 'Watch'],
            ['Fulfillment Completion', health.fulfillment, 'Live orders'],
            ['Active Seller Rate', health.seller_active, 'Approved sellers'],
            ['Registration Approval', health.registration_approval, 'Reviewed accounts'],
        ];

        panel.querySelectorAll('.admin-health-metric').forEach(function (card, index) {
            const item = metrics[index];
            if (!item) return;
            const ps = card.querySelectorAll('p');
            if (ps[0]) ps[0].textContent = item[0];
            if (ps[1]) ps[1].textContent = item[2];
            const value = card.querySelector('span.text-\[13px\]') || Array.from(card.querySelectorAll('span')).find(function (node) {
                return /%/.test(node.textContent);
            });
            if (value) value.textContent = percent(item[1]);
            const track = card.querySelector('.admin-health-track > span');
            if (track) track.style.width = Math.max(0, Math.min(100, Number(item[1] || 0))) + '%';
        });

        const subtleCards = panel.querySelectorAll('.admin-control-subtle');
        const bottom = [
            ['Operational risk', health.risk || 'Low'],
            ['Admin queue', number(health.admin_queue || 0) + ' items'],
            ['Fulfillment health', percent(health.fulfillment || 0)],
        ];

        subtleCards.forEach(function (card, index) {
            if (!bottom[index]) return;
            const ps = card.querySelectorAll('p');
            if (ps[0]) ps[0].textContent = bottom[index][0];
            if (ps[1]) ps[1].textContent = bottom[index][1];
        });
    }

    function setFocus() {
        const items = liveData.focus || [];
        const rows = document.querySelectorAll('#adminFocusList [data-admin-focus-item]');
        let activeCategories = 0;

        rows.forEach(function (row, index) {
            const item = items[index];
            if (!item) {
                row.hidden = true;
                return;
            }

            row.hidden = false;
            if (Number(item.count || 0) > 0) activeCategories++;

            row.dataset.title = item.title || '';
            row.dataset.role = item.role || '';
            row.dataset.severity = (item.severity || 'low').replace(/^./, function (c) { return c.toUpperCase(); });
            row.dataset.detail = item.detail || '';
            row.dataset.action = item.action || '';
            row.dataset.relatedUrl = item.url || '';

            const count = row.querySelector('.rounded-full .text-\[9px\]') || row.querySelector('.rounded-full span');
            if (count) count.textContent = number(item.count);

            const title = Array.from(row.querySelectorAll('span')).find(function (node) {
                return node.classList.contains('min-w-0');
            });
            if (title) title.textContent = item.title;

            const severity = row.querySelector('.admin-severity');
            if (severity) {
                severity.textContent = row.dataset.severity;
                severity.classList.remove('high', 'medium', 'low');
                severity.classList.add(item.severity || 'low');
            }
        });

        const countNode = document.getElementById('adminFocusCount');
        if (countNode) countNode.textContent = number(activeCategories);
    }

    function setRoles() {
        const roleData = liveData.roles || {};
        const map = {
            'Sellers': ['sellers', liveData.urls?.sellers],
            'Buyers': ['buyers', (liveData.urls?.users || '') + '?role=buyer'],
            'Logistics': ['logistics', (liveData.urls?.users || '') + '?role=logistics'],
            'Riders': ['riders', (liveData.urls?.users || '') + '?role=rider'],
        };

        document.querySelectorAll('#adminRoleControl .admin-role-card').forEach(function (card) {
            const ps = card.querySelectorAll('p');
            const name = ps[0]?.textContent.trim();
            const spec = map[name];
            if (!spec) return;

            const data = roleData[spec[0]] || {};
            if (ps[1]) ps[1].textContent = number(data.value);
            if (ps[2]) ps[2].textContent = data.issue || '0 pending';

            if (spec[1]) {
                card.addEventListener('click', function (event) {
                    event.preventDefault();
                    event.stopImmediatePropagation();
                    window.location.href = spec[1];
                }, true);
            }
        });
    }

    function setJourney() {
        const data = liveData.journey || {};
        const ordered = [
            data.new,
            data.preparing,
            data.ready,
            data.pickup,
            data.in_transit,
            data.delivered,
        ];

        document.querySelectorAll('#adminOrderJourney .admin-journey-stage').forEach(function (stage, index) {
            const ps = stage.querySelectorAll('p');
            if (ps[0]) ps[0].textContent = number(ordered[index] || 0);
        });
    }

    function setRiders() {
        const data = liveData.riders || {};
        const panel = document.getElementById('adminRiderOverview');
        if (!panel) return;

        const ringValue = panel.querySelector('.admin-rider-ring p');
        if (ringValue) ringValue.textContent = number(data.total);

        const stats = [
            ['Online', data.online],
            ['On Delivery', data.on_delivery],
            ['Idle', data.idle],
            ['Offline', data.offline],
            ['Overloaded', data.overloaded],
            ['Active Logistics', data.active_logistics],
        ];

        const statRows = Array.from(panel.querySelectorAll('.grid.grid-cols-2 > div')).filter(function (row) {
            return row.querySelector('.h-2.w-2.rounded-full');
        });

        statRows.forEach(function (row, index) {
            const item = stats[index];
            if (!item) return;
            const spans = row.querySelectorAll('span');
            if (spans[1]) spans[1].textContent = item[0];
            if (spans[2]) spans[2].textContent = number(item[1]);
        });

        const bottomCards = Array.from(panel.querySelectorAll('.admin-control-subtle')).slice(-3);
        const bottom = [
            ['Ready pickups', data.ready_pickup],
            ['In transit', data.in_transit],
            ['Open issues', data.open_issues],
        ];

        bottomCards.forEach(function (card, index) {
            const item = bottom[index];
            if (!item) return;
            const ps = card.querySelectorAll('p');
            if (ps[0]) ps[0].textContent = item[0];
            if (ps[1]) ps[1].textContent = number(item[1]);
        });

        const manage = Array.from(panel.querySelectorAll('button')).find(function (button) {
            return button.textContent.trim() === 'Manage';
        });
        if (manage && liveData.urls?.users) {
            manage.addEventListener('click', function (event) {
                event.preventDefault();
                event.stopImmediatePropagation();
                window.location.href = liveData.urls.users + '?role=rider';
            }, true);
        }
    }

    function chartY(value, max) {
        const top = 42;
        const bottom = 242;
        return bottom - ((Number(value || 0) / Math.max(1, max)) * (bottom - top));
    }

    function setSalesChart() {
        const data = liveData.sales || {};
        const section = document.getElementById('adminSalesOverview');
        if (!section) return;

        const topKpis = section.querySelectorAll('.sari-sales-kpi');
        const topValues = [
            [number(data.month_orders), percent(data.orders_growth, true), 'from last month'],
            [percent(data.growth, true), data.growth >= 0 ? '▲ Higher' : '▼ Lower', 'than last month'],
            [currency(data.commission, true), percent(data.commission_rate), 'commission rate'],
        ];

        topKpis.forEach(function (card, index) {
            const item = topValues[index];
            if (!item) return;
            const value = card.querySelector('.sari-sales-kpi-value');
            if (value) value.textContent = item[0];
            const trend = card.querySelector('.sari-sales-trend');
            const strong = trend?.querySelector('strong');
            const span = trend?.querySelector('span');
            if (strong) strong.textContent = item[1];
            if (span) span.textContent = item[2];
        });

        const insights = section.querySelectorAll('.sari-sales-insight');
        const insightData = [
            ['Current Sales', currency(data.current_sales), percent(data.growth, true) + ' from last month'],
            ['Growth', percent(data.growth, true), 'Compared to previous month'],
            ['Commission', currency(data.commission), percent(data.commission_rate) + ' commission rate'],
        ];

        insights.forEach(function (card, index) {
            const item = insightData[index];
            if (!item) return;
            const ps = card.querySelectorAll('p');
            if (ps[0]) ps[0].textContent = item[0];
            const value = card.querySelector('.sari-sales-insight-value');
            if (value) value.textContent = item[1];
            const note = card.querySelector('.sari-sales-insight-note');
            if (note) note.textContent = item[2];
        });

        const sales = Array.from(data.weekly_sales || [0,0,0,0,0]).slice(0, 5);
        const commissions = Array.from(data.weekly_commission || [0,0,0,0,0]).slice(0, 5);
        while (sales.length < 5) sales.push(0);
        while (commissions.length < 5) commissions.push(0);

        const maxValue = Math.max(1, ...sales, ...commissions) * 1.12;
        const xs = [115, 290, 465, 640, 815];
        const salesPoints = sales.map(function (value, index) { return [xs[index], chartY(value, maxValue)]; });
        const commissionPoints = commissions.map(function (value, index) { return [xs[index], chartY(value, maxValue)]; });

        const linePath = function (points) {
            return points.map(function (point, index) {
                return (index ? 'L' : 'M') + point[0] + ' ' + point[1].toFixed(2);
            }).join(' ');
        };

        const salesLine = section.querySelector('path.sales-line');
        const commissionLine = section.querySelector('path.commission-line');
        const trendFill = section.querySelector('path.trend-fill');
        if (salesLine) salesLine.setAttribute('d', linePath(salesPoints));
        if (commissionLine) commissionLine.setAttribute('d', linePath(commissionPoints));
        if (trendFill) {
            trendFill.setAttribute('d', linePath(salesPoints) + ' L815 242 L115 242 Z');
        }

        const salesCircles = section.querySelectorAll('circle.sales-point');
        salesPoints.forEach(function (point, index) {
            if (!salesCircles[index]) return;
            salesCircles[index].setAttribute('cx', point[0]);
            salesCircles[index].setAttribute('cy', point[1]);
        });
        if (salesCircles[5]) {
            salesCircles[5].setAttribute('cx', salesPoints[4][0]);
            salesCircles[5].setAttribute('cy', salesPoints[4][1]);
        }

        const commissionCircles = section.querySelectorAll('circle.commission-point');
        commissionPoints.forEach(function (point, index) {
            if (!commissionCircles[index]) return;
            commissionCircles[index].setAttribute('cx', point[0]);
            commissionCircles[index].setAttribute('cy', point[1]);
        });

        const latestRing = section.querySelector('circle.latest-point-ring');
        const latestGuide = section.querySelector('line.latest-guide');
        if (latestRing) latestRing.setAttribute('cy', salesPoints[4][1]);
        if (latestGuide) {
            latestGuide.setAttribute('y1', salesPoints[4][1]);
            latestGuide.setAttribute('y2', 242);
        }

        const tooltipTexts = section.querySelectorAll('.sari-sales-tooltip-card text');
        if (tooltipTexts[0]) tooltipTexts[0].textContent = 'Week 5';
        if (tooltipTexts[1]) tooltipTexts[1].textContent = currency(sales[4]);

        const axisLabels = section.querySelectorAll('text.axis-label');
        const yValues = [maxValue, maxValue * .75, maxValue * .5, maxValue * .25, 0];
        yValues.forEach(function (value, index) {
            if (axisLabels[index]) axisLabels[index].textContent = currency(value, true);
        });

        const periodButton = section.querySelector('.sari-sales-period');
        if (periodButton && liveData.urls?.reports) {
            periodButton.title = 'Open detailed reports';
            periodButton.addEventListener('click', function () {
                window.location.href = liveData.urls.reports;
            }, { once: true });
        }
    }

    function setRecentRegistrations() {
        const panel = findPanelByTitle('Recent Registrations');
        if (!panel) return;

        const subtitle = panel.querySelector('h3 + p');
        if (subtitle) subtitle.textContent = 'Latest account applications';

        const rowsContainer = Array.from(panel.querySelectorAll('div')).find(function (node) {
            return node.classList.contains('divide-y');
        });
        const rows = rowsContainer ? Array.from(rowsContainer.children) : [];
        const data = liveData.recent_registrations || [];

        rows.forEach(function (row, index) {
            const item = data[index];
            if (!item) {
                row.hidden = true;
                return;
            }
            row.hidden = false;
            const avatar = row.querySelector('.rounded-full');
            if (avatar) avatar.textContent = item.initial || 'S';
            const ps = row.querySelectorAll('p');
            if (ps[0]) ps[0].textContent = item.name || 'Applicant';
            if (ps[1]) ps[1].textContent = item.role || 'User';
            const time = row.querySelector('.2xl\\:inline');
            if (time) time.textContent = item.time || 'Recently';
            const status = Array.from(row.querySelectorAll('span')).find(function (node) {
                return ['Pending', 'Approved', 'Rejected'].includes(node.textContent.trim());
            });
            if (status) status.textContent = item.status || 'Pending';
        });

        const viewAll = Array.from(panel.querySelectorAll('button')).find(function (button) {
            return button.textContent.trim() === 'View All';
        });
        if (viewAll && liveData.urls?.registrations) {
            viewAll.addEventListener('click', function () {
                window.location.href = liveData.urls.registrations;
            });
        }
    }

    function setCategories() {
        const panel = document.getElementById('adminCategoryBreakdown');
        if (!panel) return;
        const data = liveData.categories || { total: 0, items: [] };

        const center = panel.querySelector('.admin-donut-premium-center');
        const centerPs = center?.querySelectorAll('p') || [];
        if (centerPs[1]) centerPs[1].textContent = number(data.total || 0);
        if (centerPs[2]) centerPs[2].textContent = number((data.items || []).length) + ' categories';

        const rows = Array.from(panel.querySelectorAll('.admin-legend-row'));
        const palette = ['#d7a325', '#284b73', '#2f9b7c', '#c38a42'];
        rows.forEach(function (row, index) {
            const item = (data.items || [])[index];
            if (!item) {
                row.hidden = true;
                return;
            }
            row.hidden = false;
            const label = row.querySelector('.text-\[10px\].font-medium');
            if (label) label.textContent = item.name;
            const value = Array.from(row.querySelectorAll('span')).find(function (node) {
                return node.classList.contains('font-bold');
            });
            if (value) value.textContent = percent(item.percent);
            const dot = row.querySelector('.h-2\.5.w-2\.5');
            const bar = row.querySelector('.admin-legend-bar > span');
            const color = palette[index] || palette[0];
            if (dot) dot.style.background = color;
            if (bar) {
                bar.style.background = color;
                bar.style.width = Math.max(0, Math.min(100, Number(item.percent || 0))) + '%';
            }
        });

        const button = Array.from(panel.querySelectorAll('button')).find(function (node) {
            return node.textContent.includes('View All Categories');
        });
        if (button && liveData.urls?.seller_compliance) {
            button.addEventListener('click', function () {
                window.location.href = liveData.urls.seller_compliance;
            });
        }
    }

    function setActivity() {
        const panel = findPanelByTitle('Platform Activity');
        if (!panel) return;
        const data = liveData.activity || [];
        const rows = Array.from(panel.querySelectorAll('.mt-4.space-y-1 > div'));

        rows.forEach(function (row, index) {
            const item = data[index];
            if (!item) {
                row.hidden = true;
                return;
            }
            row.hidden = false;
            const ps = row.querySelectorAll('p');
            if (ps[0]) ps[0].textContent = item.title || 'Activity';
            if (ps[1]) ps[1].textContent = item.body || '';
            const time = Array.from(row.querySelectorAll('span')).find(function (node) {
                return node.classList.contains('shrink-0') && node.textContent.trim();
            });
            if (time) time.textContent = item.time || 'Recently';
        });

        const viewAll = Array.from(panel.querySelectorAll('button')).find(function (button) {
            return button.textContent.trim() === 'View All';
        });
        if (viewAll && liveData.urls?.reports) {
            viewAll.addEventListener('click', function () {
                window.location.href = liveData.urls.reports;
            });
        }
    }

    function wireFocusDrawer() {
        let relatedUrl = liveData.urls?.reports || '/admin/reports';

        document.querySelectorAll('[data-admin-focus-item]').forEach(function (row) {
            row.addEventListener('click', function () {
                relatedUrl = row.dataset.relatedUrl || relatedUrl;
            });
        });

        const reviewed = document.getElementById('adminMarkReviewed');
        const openRelated = document.getElementById('adminOpenRelated');
        const drawer = document.getElementById('adminFocusDrawer');
        const note = drawer?.querySelector('.mt-3.text-\[8px\]');

        if (reviewed) reviewed.textContent = 'Open queue';
        if (note) note.textContent = 'This opens the real management area backed by the database; no browser-only review state is used.';

        [reviewed, openRelated].forEach(function (button) {
            if (!button) return;
            button.addEventListener('click', function (event) {
                event.preventDefault();
                event.stopImmediatePropagation();
                window.location.href = relatedUrl;
            }, true);
        });
    }

    function applyFunctionalDashboard() {
        if (!liveData || !Object.keys(liveData).length) return;
        setSummaryKpis();
        setSystemCard();
        setHealth();
        setFocus();
        setRoles();
        setJourney();
        setRiders();
        setSalesChart();
        setRecentRegistrations();
        setCategories();
        setActivity();
        wireFocusDrawer();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', applyFunctionalDashboard, { once: true });
    } else {
        applyFunctionalDashboard();
    }

    document.addEventListener('livewire:navigated', applyFunctionalDashboard);
})();
</script>
