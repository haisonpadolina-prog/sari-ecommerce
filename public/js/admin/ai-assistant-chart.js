(() => {
    'use strict';
    const NS = 'http://www.w3.org/2000/svg';
    let sequence = 0;
    function render(data, options) {
        const { t, locale, download } = options;
        const node = (tag, text, cls) => {
            const n = document.createElement(tag); if (text !== undefined) n.textContent = text; if (cls) n.className = cls; return n;
        };
        const svgNode = (tag, attrs = {}, text) => {
            const n = document.createElementNS(NS, tag); Object.entries(attrs).forEach(([k, v]) => n.setAttribute(k, String(v)));
            if (text !== undefined) n.textContent = text; return n;
        };
        const all = (data.rows || []).filter(r => Number.isFinite(Number(r.value))).map(r => ({ label: String(r.label), value: Number(r.value) }));
        const temporal = ['day', 'month'].includes(data.group);
        const rows = temporal ? all : all.slice(0, 20);
        const empty = data.matching_records === 0 || rows.length === 0;
        const groupName = t('chart_' + (data.group || 'seller'));
        const metricName = data.metric ? t('metric_' + data.metric) : String(data.title || t('analytics'));
        const title = data.metric ? metricName + ' · ' + groupName : metricName;
        const range = [data.from, data.to].filter(Boolean).join(' — ') + (data.timezone ? ' · ' + data.timezone : '');
        const language = locale === 'tl' ? 'fil-PH' : 'en-PH';
        const money = value => new Intl.NumberFormat(language, { style: 'currency', currency: 'PHP', maximumFractionDigits: 2 }).format(value);
        const valueText = value => data.unit === 'PHP' ? money(value) : new Intl.NumberFormat(language, { maximumFractionDigits: 2 }).format(value);
        const tickText = value => new Intl.NumberFormat(language, { notation: 'compact', maximumFractionDigits: 2 }).format(value);
        const shortLabel = label => {
            if (temporal && /^\d{4}-\d{2}(-\d{2})?$/.test(label)) {
                const parts = label.split('-').map(Number), date = new Date(Date.UTC(parts[0], parts[1] - 1, parts[2] || 1));
                return new Intl.DateTimeFormat(language, { month: 'short', ...(data.group === 'day' ? { day: 'numeric' } : { year: '2-digit' }), timeZone: 'UTC' }).format(date);
            }
            return label.length > 14 ? label.slice(0, 13) + '…' : label;
        };
        const wrap = node('div', undefined, 'sai-chart');
        const controls = node('div', undefined, 'sai-chart-controls');
        const legend = node('span', metricName, 'sai-chart-legend'); controls.append(legend);
        const plot = node('div', undefined, 'sai-chart-plot');
        const tooltip = node('div', '', 'sai-chart-tooltip'); tooltip.hidden = true; tooltip.setAttribute('role', 'status');
        const svg = svgNode('svg', { viewBox: '0 0 900 420', role: 'img', 'aria-label': title + '. ' + range });
        let type = temporal ? 'line' : 'bar';
        const modeButtons = [];
        ['line', 'bar'].forEach(mode => {
            const b = node('button', t('chart_' + mode), 'sai-chart-switch'); b.type = 'button';
            b.addEventListener('click', () => { type = mode; draw(); }); modeButtons.push([mode, b]); controls.append(b);
        });
        function draw() {
            svg.replaceChildren(); tooltip.hidden = true;
            modeButtons.forEach(([mode, b]) => b.setAttribute('aria-pressed', String(mode === type)));
            const W = 900, H = 420, left = 92, right = 860, top = 100, bottom = 325;
            svg.append(svgNode('rect', { width: W, height: H, rx: 12, fill: '#ffffff' }));
            const text = (x, y, value, attrs = {}) => svg.append(svgNode('text', { x, y, fill: '#718198', 'font-size': 20, 'font-family': 'Arial, sans-serif', ...attrs }, value));
            text(24, 32, title, { fill: '#34465f', 'font-size': 28, 'font-weight': 600 });
            text(24, 59, range, { 'font-size': 17 });
            text(left, 82, data.unit === 'PHP' ? 'PHP' : t('orders'), { 'font-size': 16, 'font-weight': 600 });
            let low = Math.min(0, ...rows.map(r => r.value)), high = Math.max(0, ...rows.map(r => r.value));
            if (low === high) high = low + 1;
            const rawStep = (high - low) / 4, magnitude = 10 ** Math.floor(Math.log10(rawStep));
            const step = [1, 2, 5, 10].find(n => n * magnitude >= rawStep) * magnitude;
            low = Math.floor(low / step) * step; high = Math.ceil(high / step) * step;
            const y = value => bottom - (value - low) / (high - low) * (bottom - top);
            const x = index => type === 'bar' ? left + (index + .5) * (right - left) / Math.max(1, rows.length)
                : rows.length === 1 ? (left + right) / 2 : left + index * (right - left) / Math.max(1, rows.length - 1);
            for (let value = low, count = 0; value <= high + step / 10 && count < 8; value += step, count++) {
                const pos = y(value);
                svg.append(svgNode('line', { x1: left, x2: right, y1: pos, y2: pos, stroke: '#e9eef4', 'stroke-dasharray': '4 5' }));
                text(left - 12, pos + 4, tickText(Math.abs(value) < step / 100 ? 0 : value), { 'text-anchor': 'end', 'font-size': 18 });
            }
            svg.append(svgNode('line', { x1: left, x2: right, y1: y(0), y2: y(0), stroke: '#cfd9e4' }));
            svg.append(svgNode('line', { x1: left, x2: left, y1: top, y2: bottom, stroke: '#dce4ed' }));
            const stride = Math.max(1, Math.ceil(rows.length / 7));
            rows.forEach((r, i) => {
                if (i % stride === 0 || i === rows.length - 1) text(x(i), bottom + 24, shortLabel(r.label), { 'text-anchor': 'middle', 'font-size': 18 });
            });
            text((left + right) / 2, bottom + 54, groupName + (data.timezone && temporal ? ' · ' + data.timezone : ''), { 'text-anchor': 'middle', 'font-size': 18 });
            if (type === 'line' && rows.length) {
                const path = rows.map((r, i) => (i ? 'L' : 'M') + x(i) + ',' + y(r.value)).join(' ');
                const id = 'sariChartFill' + (++sequence), defs = svgNode('defs'), gradient = svgNode('linearGradient', { id, x1: 0, y1: 0, x2: 0, y2: 1 });
                gradient.append(svgNode('stop', { offset: '0%', 'stop-color': '#b98423', 'stop-opacity': .17 }), svgNode('stop', { offset: '100%', 'stop-color': '#b98423', 'stop-opacity': 0 }));
                defs.append(gradient); svg.append(defs);
                svg.append(svgNode('path', { d: path + ' L' + x(rows.length - 1) + ',' + y(0) + ' L' + x(0) + ',' + y(0) + ' Z', fill: 'url(#' + id + ')' }));
                svg.append(svgNode('path', { d: path, fill: 'none', stroke: empty ? '#a5b3c5' : '#b98423', 'stroke-width': 2.8, 'stroke-linejoin': 'round', ...(empty ? { 'stroke-dasharray': '5 5' } : {}), 'data-chart-series': 'line' }));
            }
            const points = [];
            rows.forEach((r, i) => {
                const mark = type === 'bar' ? svgNode('rect', { x: x(i) - Math.min(36, (right - left) / Math.max(rows.length, 1) * .55) / 2,
                    y: Math.min(y(0), y(r.value)), width: Math.min(36, (right - left) / Math.max(rows.length, 1) * .55),
                    height: Math.max(r.value === 0 ? 0 : 1, Math.abs(y(r.value) - y(0))), rx: 3, fill: '#bc8a32', 'data-chart-series': 'bar' })
                    : svgNode('circle', { cx: x(i), cy: y(r.value), r: rows.length > 50 ? 2.5 : 4, fill: '#ffffff', stroke: empty ? '#a5b3c5' : '#b98423', 'stroke-width': 2 });
                const point = svgNode('g', { tabindex: i === 0 ? 0 : -1, role: 'img', 'aria-label': r.label + ': ' + valueText(r.value), 'data-chart-point': i });
                point.append(mark, svgNode('circle', { cx: x(i), cy: y(r.value), r: 12, fill: 'transparent' }), svgNode('title', {}, r.label + ': ' + valueText(r.value)));
                const show = () => { tooltip.textContent = r.label + ' · ' + valueText(r.value); tooltip.hidden = false; };
                point.addEventListener('pointerenter', show); point.addEventListener('focus', show);
                point.addEventListener('pointerleave', () => { tooltip.hidden = true; }); point.addEventListener('blur', () => { tooltip.hidden = true; });
                point.addEventListener('keydown', event => {
                    const next = event.key === 'ArrowRight' ? Math.min(rows.length - 1, i + 1) : event.key === 'ArrowLeft' ? Math.max(0, i - 1)
                        : event.key === 'Home' ? 0 : event.key === 'End' ? rows.length - 1 : null;
                    if (next !== null) { event.preventDefault(); points.forEach((p, j) => p.setAttribute('tabindex', j === next ? 0 : -1)); points[next].focus(); }
                });
                points.push(point); svg.append(point);
            });
            if (empty) {
                svg.append(svgNode('rect', { x: 250, y: 161, width: 450, height: 54, rx: 12, fill: '#f5f8fc', stroke: '#e5ebf3' }));
                text(475, 193, t('chart_empty_short'), { 'text-anchor': 'middle', fill: '#7a8ba2', 'font-size': 21 });
            }
            text(24, 405, 'SARI · ' + (data.date_basis ? t('chart_basis') + ': ' + data.date_basis : t('chart_values')), { 'font-size': 16 });
        }
        draw(); plot.append(svg, tooltip); wrap.append(controls, plot);
        if (empty) wrap.append(node('p', t(temporal ? 'chart_empty_dates' : 'chart_empty'), 'sai-chart-note'));
        if (data.undated_delivered_records > 0) wrap.append(node('p', t('chart_undated', { count: data.undated_delivered_records }), 'sai-chart-note'));
        if (!temporal && all.length > 20) wrap.append(node('p', t('chart_note'), 'sai-chart-note'));
        const exportButton = node('button', t('png'), 'sai-small'); exportButton.type = 'button';
        exportButton.addEventListener('click', () => {
            const blob = new Blob([new XMLSerializer().serializeToString(svg)], { type: 'image/svg+xml;charset=utf-8' });
            const url = URL.createObjectURL(blob), image = new Image(); exportButton.disabled = true;
            image.onload = () => {
                try {
                    const canvas = document.createElement('canvas'); canvas.width = 1800; canvas.height = 840;
                    const ctx = canvas.getContext('2d'); if (!ctx) throw new Error('Canvas unavailable');
                    ctx.drawImage(image, 0, 0, 1800, 840); canvas.toBlob(file => { if (file) download(file, 'sari-chart.png'); else exportButton.textContent = t('chart_export_error'); });
                } catch (_) { exportButton.textContent = t('chart_export_error');
                } finally { URL.revokeObjectURL(url); exportButton.disabled = false; }
            };
            image.onerror = () => { URL.revokeObjectURL(url); exportButton.disabled = false; exportButton.textContent = t('chart_export_error'); };
            image.src = url;
        });
        wrap.append(exportButton);
        return wrap;
    }
    window.SariAdminAiChart = { render };
})();
