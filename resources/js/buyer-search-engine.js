const DEFAULT_SYNONYM_GROUPS = [
    ['perfume', 'fragrance', 'cologne', 'pabango'],
    ['shoe', 'shoes', 'sneaker', 'sneakers', 'footwear', 'sapatos'],
    ['shirt', 'shirts', 'tshirt', 't-shirt', 'tee', 'polo'],
    ['pants', 'trousers', 'slacks'],
    ['phone', 'cellphone', 'mobile', 'smartphone'],
    ['bag', 'handbag', 'purse'],
    ['beauty', 'cosmetics', 'makeup'],
    ['women', 'woman', 'female', 'babae', 'pambabae'],
    ['men', 'man', 'male', 'lalaki', 'panlalaki', 'panglalaki'],
    ['kid', 'kids', 'child', 'children', 'bata', 'pambata'],
    ['cheap', 'budget', 'affordable', 'mura', 'murang'],
    ['gift', 'present', 'regalo', 'pangregalo'],
    ['office', 'formal', 'workwear', 'pangoffice'],
];

const COMMON_COLORS = [
    'black', 'white', 'gray', 'grey', 'red', 'blue', 'green', 'yellow', 'orange',
    'purple', 'pink', 'brown', 'beige', 'cream', 'gold', 'silver', 'navy', 'maroon',
    'teal', 'olive', 'khaki', 'violet', 'rose', 'tan', 'multicolor', 'multi-color',
];

const SIZE_ALIASES = new Map([
    ['extra small', 'xs'], ['x-small', 'xs'], ['xs', 'xs'],
    ['small', 's'], ['s', 's'],
    ['medium', 'm'], ['m', 'm'],
    ['large', 'l'], ['l', 'l'],
    ['extra large', 'xl'], ['x-large', 'xl'], ['xl', 'xl'],
    ['xxl', 'xxl'], ['2xl', 'xxl'],
    ['xxxl', 'xxxl'], ['3xl', 'xxxl'],
]);

export function normalizeSearch(value) {
    return String(value ?? '')
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .toLowerCase()
        .replace(/₱/g, ' p ')
        .replace(/[^a-z0-9.,+&/-]+/g, ' ')
        .replace(/\s+/g, ' ')
        .trim();
}

function tokenise(value) {
    return normalizeSearch(value)
        .split(' ')
        .map((token) => token.trim())
        .filter(Boolean);
}

function unique(values) {
    return [...new Set(values.filter(Boolean))];
}

function flattenOptionValues(optionGroups) {
    if (!optionGroups || typeof optionGroups !== 'object') return [];

    return Object.values(optionGroups)
        .flatMap((values) => Array.isArray(values) ? values : [values])
        .map((value) => String(value ?? '').trim())
        .filter(Boolean);
}

function optionValues(optionGroups, keys) {
    if (!optionGroups || typeof optionGroups !== 'object') return [];
    const wanted = new Set(keys.map((key) => normalizeSearch(key)));

    return Object.entries(optionGroups)
        .filter(([key]) => wanted.has(normalizeSearch(key)))
        .flatMap(([, values]) => Array.isArray(values) ? values : [values])
        .map((value) => String(value ?? '').trim())
        .filter(Boolean);
}

function levenshtein(left, right) {
    const a = String(left ?? '');
    const b = String(right ?? '');

    if (a === b) return 0;
    if (!a.length) return b.length;
    if (!b.length) return a.length;

    const previous = Array.from({ length: b.length + 1 }, (_, index) => index);
    const current = new Array(b.length + 1);

    for (let i = 1; i <= a.length; i += 1) {
        current[0] = i;

        for (let j = 1; j <= b.length; j += 1) {
            const cost = a[i - 1] === b[j - 1] ? 0 : 1;
            current[j] = Math.min(
                current[j - 1] + 1,
                previous[j] + 1,
                previous[j - 1] + cost,
            );
        }

        for (let j = 0; j <= b.length; j += 1) previous[j] = current[j];
    }

    return previous[b.length];
}

function maxTypoDistance(token) {
    if (token.length <= 3) return 0;
    if (token.length <= 5) return 1;
    if (token.length <= 9) return 2;
    return 3;
}

function createSynonymLookup(groups = DEFAULT_SYNONYM_GROUPS) {
    const lookup = new Map();

    groups.forEach((group) => {
        const normalized = unique(group.map(normalizeSearch));
        normalized.forEach((term) => lookup.set(term, normalized));
    });

    return lookup;
}

function parseMoney(value) {
    const parsed = Number(String(value ?? '').replace(/,/g, ''));
    return Number.isFinite(parsed) ? parsed : null;
}

function findPriceFilters(query) {
    const source = normalizeSearch(query).replace(/\bp\s*(?=\d)/g, '');
    let minPrice = null;
    let maxPrice = null;
    const consumed = [];

    const between = source.match(/\b(?:between|from)?\s*(\d[\d,]*(?:\.\d+)?)\s*(?:to|and|-)\s*(\d[\d,]*(?:\.\d+)?)\b/);
    if (between) {
        const first = parseMoney(between[1]);
        const second = parseMoney(between[2]);
        if (first !== null && second !== null) {
            minPrice = Math.min(first, second);
            maxPrice = Math.max(first, second);
            consumed.push(between[0]);
        }
    }

    const under = source.match(/\b(?:under|below|less than|up to|upto|max(?:imum)?|not over)\s*(\d[\d,]*(?:\.\d+)?)\b/)
        || source.match(/\b(\d[\d,]*(?:\.\d+)?)\s*(?:or less|and below|below)\b/);
    if (under) {
        const amount = parseMoney(under[1]);
        if (amount !== null) maxPrice = amount;
        consumed.push(under[0]);
    }

    const over = source.match(/\b(?:over|above|more than|at least|min(?:imum)?|from)\s*(\d[\d,]*(?:\.\d+)?)\b/)
        || source.match(/\b(\d[\d,]*(?:\.\d+)?)\s*(?:or more|and above|above)\b/);
    if (over && !between) {
        const amount = parseMoney(over[1]);
        if (amount !== null) minPrice = amount;
        consumed.push(over[0]);
    }

    return { minPrice, maxPrice, consumed };
}

function normalizeSize(value) {
    const normalized = normalizeSearch(value);
    return SIZE_ALIASES.get(normalized) || normalized;
}

function findSizeFilter(query, knownSizes) {
    const normalized = normalizeSearch(query);

    const explicit = normalized.match(/\b(?:size|sz)\s*([a-z0-9-]+)\b/);
    if (explicit) {
        return { size: normalizeSize(explicit[1]), consumed: explicit[0] };
    }

    const aliases = [...SIZE_ALIASES.keys()].sort((a, b) => b.length - a.length);
    for (const alias of aliases) {
        const pattern = new RegExp(`\\b${alias.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')}\\b`);
        if (pattern.test(normalized)) {
            return { size: normalizeSize(alias), consumed: alias };
        }
    }

    for (const size of knownSizes) {
        const normalizedSize = normalizeSize(size);
        if (!normalizedSize || /^\d+(?:\.\d+)?$/.test(normalizedSize)) continue;
        const pattern = new RegExp(`\\b${normalizedSize.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')}\\b`);
        if (pattern.test(normalized)) {
            return { size: normalizedSize, consumed: normalizedSize };
        }
    }

    return { size: null, consumed: null };
}

function findColorFilter(query, knownColors) {
    const normalized = normalizeSearch(query);
    const colors = unique([...COMMON_COLORS, ...knownColors.map(normalizeSearch)])
        .sort((a, b) => b.length - a.length);

    for (const color of colors) {
        if (!color) continue;
        const pattern = new RegExp(`\\b${color.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')}\\b`);
        if (pattern.test(normalized)) return { color, consumed: color };
    }

    return { color: null, consumed: null };
}

function stripConsumed(query, consumed) {
    let value = ` ${normalizeSearch(query)} `;

    consumed.filter(Boolean).forEach((fragment) => {
        const needle = ` ${normalizeSearch(fragment)} `;
        value = value.replace(needle, ' ');
    });

    value = value
        .replace(/\b(?:in stock|available now|available)\b/g, ' ')
        .replace(/\s+/g, ' ')
        .trim();

    return value;
}

function phraseScore(haystack, query, exact, starts, includes) {
    if (!query || !haystack) return 0;
    if (haystack === query) return exact;
    if (haystack.startsWith(query)) return starts;
    if (haystack.includes(query)) return includes;
    return 0;
}

function tokenScore(haystack, token, exact, starts, includes) {
    if (!token || !haystack) return 0;
    const tokens = tokenise(haystack);
    if (tokens.includes(token)) return exact;
    if (tokens.some((candidate) => candidate.startsWith(token))) return starts;
    if (haystack.includes(token)) return includes;
    return 0;
}

function entityMatches(entity, query) {
    if (!query) return false;
    const normalized = normalizeSearch(entity);
    return normalized === query || normalized.startsWith(query) || normalized.includes(query);
}

export function createSearchEngine(rawProducts = [], options = {}) {
    const synonymLookup = createSynonymLookup(options.synonyms || DEFAULT_SYNONYM_GROUPS);

    const products = rawProducts.map((product) => {
        const optionGroups = product.option_groups && typeof product.option_groups === 'object'
            ? product.option_groups
            : {};
        const colors = unique([
            ...(Array.isArray(product.colors) ? product.colors : []),
            ...optionValues(optionGroups, ['color', 'colour']),
        ]);
        const sizes = unique([
            ...(Array.isArray(product.sizes) ? product.sizes : []),
            ...optionValues(optionGroups, ['size', 'shoe size']),
        ]);
        const optionValuesFlat = unique([
            ...(Array.isArray(product.variations) ? product.variations : []),
            ...flattenOptionValues(optionGroups),
        ]);

        return {
            ...product,
            id: Number(product.id || 0),
            price: Number(product.price || 0),
            stock: Number(product.stock || 0),
            rating: Number(product.rating || 0),
            sold: Number(product.sold || 0),
            nameSearch: normalizeSearch(product.name),
            brandSearch: normalizeSearch(product.brand),
            categorySearch: normalizeSearch(product.category),
            sellerSearch: normalizeSearch(product.store_name || product.shop_name),
            descriptionSearch: normalizeSearch(product.description),
            optionsSearch: normalizeSearch(optionValuesFlat.join(' ')),
            colors: colors.map(normalizeSearch),
            sizes: sizes.map(normalizeSize),
            optionValues: optionValuesFlat.map(normalizeSearch),
        };
    }).filter((product) => product.id > 0);

    const maxSold = Math.max(1, ...products.map((product) => product.sold));
    const knownColors = unique(products.flatMap((product) => product.colors));
    const knownSizes = unique(products.flatMap((product) => product.sizes));

    const entityValues = {
        categories: unique(products.map((product) => String(product.category || '').trim())),
        brands: unique(products.map((product) => String(product.brand || '').trim())),
        sellers: unique(products.map((product) => String(product.store_name || product.shop_name || '').trim())),
    };

    const vocabulary = new Set();
    products.forEach((product) => {
        [product.nameSearch, product.brandSearch, product.categorySearch, product.sellerSearch, product.optionsSearch]
            .flatMap(tokenise)
            .forEach((token) => {
                if (token.length >= 3 && !/^\d+$/.test(token)) vocabulary.add(token);
            });
    });
    synonymLookup.forEach((terms) => terms.forEach((term) => vocabulary.add(term)));

    function correctToken(token) {
        if (!token || token.length < 4 || /^\d/.test(token)) return token;
        if (vocabulary.has(token)) return token;
        if ([...vocabulary].some((candidate) => candidate.startsWith(token))) return token;

        const maxDistance = maxTypoDistance(token);
        if (maxDistance <= 0) return token;

        let best = token;
        let bestDistance = maxDistance + 1;

        vocabulary.forEach((candidate) => {
            if (Math.abs(candidate.length - token.length) > maxDistance) return;
            const distance = levenshtein(token, candidate);
            if (distance < bestDistance) {
                best = candidate;
                bestDistance = distance;
            }
        });

        return bestDistance <= maxDistance ? best : token;
    }

    function parse(query) {
        const normalized = normalizeSearch(query);
        const price = findPriceFilters(query);
        const size = findSizeFilter(query, knownSizes);
        const color = findColorFilter(query, knownColors);
        const inStockOnly = /\b(?:in stock|available now)\b/.test(normalized);
        const consumed = [...price.consumed, size.consumed, color.consumed];
        const textualQuery = stripConsumed(query, consumed);
        const originalTokens = tokenise(textualQuery).filter((token) => token !== 'p');
        const correctedTokens = originalTokens.map(correctToken);
        const correctedQuery = correctedTokens.join(' ');
        const expandedTokens = unique(correctedTokens.flatMap((token) => synonymLookup.get(token) || [token]));

        return {
            raw: String(query ?? '').trim(),
            normalized,
            textualQuery,
            originalTokens,
            correctedTokens,
            correctedQuery,
            expandedTokens,
            corrected: originalTokens.join(' ') !== correctedQuery,
            minPrice: price.minPrice,
            maxPrice: price.maxPrice,
            color: color.color,
            size: size.size,
            inStockOnly,
        };
    }

    function evaluate(product, parsed) {
        if (parsed.minPrice !== null && product.price < parsed.minPrice) return null;
        if (parsed.maxPrice !== null && product.price > parsed.maxPrice) return null;
        if (parsed.inStockOnly && product.stock <= 0) return null;

        if (parsed.color) {
            const colorMatch = product.colors.includes(parsed.color)
                || product.optionValues.some((value) => value === parsed.color || value.includes(parsed.color));
            if (!colorMatch) return null;
        }

        if (parsed.size) {
            const wantedSize = normalizeSize(parsed.size);
            const sizeMatch = product.sizes.some((value) => normalizeSize(value) === wantedSize)
                || product.optionValues.some((value) => normalizeSize(value) === wantedSize);
            if (!sizeMatch) return null;
        }

        const phrase = parsed.correctedQuery;
        let relevance = 0;

        relevance += phraseScore(product.nameSearch, phrase, 140, 105, 82);
        relevance += phraseScore(product.brandSearch, phrase, 70, 50, 36);
        relevance += phraseScore(product.categorySearch, phrase, 60, 42, 30);
        relevance += phraseScore(product.sellerSearch, phrase, 60, 42, 30);
        relevance += phraseScore(product.optionsSearch, phrase, 48, 34, 24);
        relevance += phraseScore(product.descriptionSearch, phrase, 24, 18, 12);

        parsed.expandedTokens.forEach((token) => {
            relevance += tokenScore(product.nameSearch, token, 24, 17, 10);
            relevance += tokenScore(product.brandSearch, token, 16, 12, 7);
            relevance += tokenScore(product.categorySearch, token, 14, 10, 6);
            relevance += tokenScore(product.sellerSearch, token, 14, 10, 6);
            relevance += tokenScore(product.optionsSearch, token, 13, 9, 5);
            relevance += tokenScore(product.descriptionSearch, token, 5, 3, 2);
        });

        const hasText = parsed.correctedTokens.length > 0;
        if (hasText && relevance <= 0) return null;

        const ratingScore = Math.max(0, Math.min(5, product.rating)) / 5 * 15;
        const soldScore = Math.min(15, (Math.log10(product.sold + 1) / Math.log10(maxSold + 1)) * 15);
        const stockScore = product.stock > 0 ? 10 : -28;
        const score = relevance + ratingScore + soldScore + stockScore;

        return { product, score, relevance, ratingScore, soldScore, stockScore };
    }

    function search(query, extra = {}) {
        const parsed = parse(query);
        const minOverride = Number.isFinite(Number(extra.minPrice)) && String(extra.minPrice) !== ''
            ? Number(extra.minPrice)
            : null;
        const maxOverride = Number.isFinite(Number(extra.maxPrice)) && String(extra.maxPrice) !== ''
            ? Number(extra.maxPrice)
            : null;

        if (minOverride !== null) parsed.minPrice = parsed.minPrice === null ? minOverride : Math.max(parsed.minPrice, minOverride);
        if (maxOverride !== null) parsed.maxPrice = parsed.maxPrice === null ? maxOverride : Math.min(parsed.maxPrice, maxOverride);
        if (extra.inStockOnly) parsed.inStockOnly = true;

        const category = normalizeSearch(extra.category || '');
        let results = products
            .map((product) => evaluate(product, parsed))
            .filter(Boolean)
            .filter(({ product }) => !category || product.categorySearch === category);

        const mode = extra.sort || 'featured';
        results.sort((left, right) => {
            if (mode === 'price-low') return left.product.price - right.product.price || right.score - left.score;
            if (mode === 'price-high') return right.product.price - left.product.price || right.score - left.score;
            if (mode === 'rating') return right.product.rating - left.product.rating || right.score - left.score;
            if (mode === 'sold') return right.product.sold - left.product.sold || right.score - left.score;
            if (mode === 'latest') return Number(right.product.id) - Number(left.product.id);
            return right.score - left.score || right.product.rating - left.product.rating || right.product.sold - left.product.sold;
        });

        return { parsed, results };
    }

    function entitySuggestions(query, limit = 4) {
        const normalized = normalizeSearch(query);
        if (!normalized) return [];

        const suggestions = [];
        const groups = [
            ['Category', entityValues.categories],
            ['Brand', entityValues.brands],
            ['Seller', entityValues.sellers],
        ];

        groups.forEach(([type, values]) => {
            values
                .filter((value) => entityMatches(value, normalized))
                .slice(0, limit)
                .forEach((value) => suggestions.push({ type, value }));
        });

        return suggestions.slice(0, limit);
    }

    function zeroResultSuggestions(query, limit = 5) {
        const parsed = parse(query);
        const suggestions = [];

        if (parsed.corrected && parsed.correctedQuery) suggestions.push(parsed.correctedQuery);

        if (parsed.correctedQuery && (parsed.color || parsed.size || parsed.minPrice !== null || parsed.maxPrice !== null)) {
            suggestions.push(parsed.correctedQuery);
        }

        parsed.correctedTokens.forEach((token) => {
            const nearby = [];
            vocabulary.forEach((candidate) => {
                const distance = levenshtein(token, candidate);
                if (distance <= Math.max(1, maxTypoDistance(token))) nearby.push({ candidate, distance });
            });
            nearby.sort((a, b) => a.distance - b.distance || a.candidate.localeCompare(b.candidate));
            nearby.slice(0, 2).forEach(({ candidate }) => suggestions.push(candidate));
        });

        entitySuggestions(query, 3).forEach(({ value }) => suggestions.push(value));

        return unique(suggestions.map((value) => String(value).trim()))
            .filter((value) => normalizeSearch(value) !== normalizeSearch(query))
            .slice(0, limit);
    }

    function autocomplete(query, limit = 6) {
        const { parsed, results } = search(query);
        return {
            parsed,
            products: results.slice(0, limit).map(({ product, score }) => ({ ...product, search_score: score })),
            entities: entitySuggestions(parsed.correctedQuery || query, 4),
            suggestions: results.length ? [] : zeroResultSuggestions(query, 5),
        };
    }

    return {
        products,
        parse,
        search,
        autocomplete,
        entitySuggestions,
        zeroResultSuggestions,
    };
}
