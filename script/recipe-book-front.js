var multiplyAmounts = (multi) => {
    var amounts = document.getElementsByClassName('rb-rm-amount');

    for (var i = 0; i < amounts.length; i++) {
        var amount = amounts[i].getAttribute('data-amount') || '';
        if (amount.trim() !== '') {
            var scaledAmount = multiplyFullNumber(amount, multi);
            if (scaledAmount !== null) {
                amounts[i].textContent = scaledAmount;
            }
        }
    }
};

/**
 * Parse decimal, mixed, slash and Unicode fraction ingredient amounts.
 * Returns null for descriptive amounts such as "to taste".
 */
var parseIngredientAmount = (value) => {
    var fractions = {
        '½': 0.5, '⅓': 1 / 3, '⅔': 2 / 3,
        '¼': 0.25, '¾': 0.75,
        '⅛': 0.125, '⅜': 0.375, '⅝': 0.625, '⅞': 0.875
    };
    var input = String(value).trim().replace(',', '.');
    if (!input) return null;

    var unicodeFraction = 0;
    Object.keys(fractions).forEach((symbol) => {
        if (input.indexOf(symbol) !== -1) {
            unicodeFraction = fractions[symbol];
            input = input.replace(symbol, '');
        }
    });

    input = input.trim();
    var mixedMatch = input.match(/^(-?\d+)\s+(\d+)\s*\/\s*(\d+)$/);
    if (mixedMatch) {
        var whole = parseInt(mixedMatch[1], 10);
        var numerator = parseInt(mixedMatch[2], 10);
        var denominator = parseInt(mixedMatch[3], 10);
        if (!denominator) return null;
        return whole + (whole < 0 ? -1 : 1) * numerator / denominator;
    }

    var fractionMatch = input.match(/^(-?\d+)\s*\/\s*(\d+)$/);
    if (fractionMatch) {
        var fractionDenominator = parseInt(fractionMatch[2], 10);
        if (!fractionDenominator) return null;
        return parseInt(fractionMatch[1], 10) / fractionDenominator;
    }

    if (input === '' && unicodeFraction) return unicodeFraction;
    if (/^-?\d+(\.\d+)?$/.test(input)) {
        return parseFloat(input) + (input.charAt(0) === '-' ? -unicodeFraction : unicodeFraction);
    }
    return null;
};

var formatIngredientAmount = (value) => {
    if (Math.abs(value) < 0.000001) return '0';

    var sign = value < 0 ? '-' : '';
    var absolute = Math.abs(value);
    var whole = Math.floor(absolute + 0.000001);
    var remainder = absolute - whole;

    if (remainder < 0.000001) return sign + String(whole);

    var fractions = [
        [1, 2, '½'], [1, 3, '⅓'], [2, 3, '⅔'],
        [1, 4, '¼'], [3, 4, '¾'],
        [1, 8, '⅛'], [3, 8, '⅜'], [5, 8, '⅝'], [7, 8, '⅞']
    ];
    var closest = null;
    var smallestDifference = Infinity;

    fractions.forEach((fraction) => {
        var difference = Math.abs(remainder - fraction[0] / fraction[1]);
        if (difference < smallestDifference) {
            smallestDifference = difference;
            closest = fraction[2];
        }
    });

    if (smallestDifference > 0.015) {
        return sign + String(Number(absolute.toFixed(3)));
    }
    return sign + (whole ? String(whole) + ' ' : '') + closest;
};

var multiplyFullNumber = (numberString, multi) => {
    var amount = parseIngredientAmount(numberString);
    if (amount === null) return null;
    return formatIngredientAmount(amount * multi);
};

var unselectAllBtns = (allmbtns, except) => {
    var multiVal = except.getAttribute('data-multi');
    for (var i = 0; i < allmbtns.length; i++) {
        var cur = allmbtns[i];
        cur.classList.remove('-selected');
        if (cur.getAttribute('data-multi') == multiVal) {
            cur.classList.add('-selected');
        }
    }
};

var recipeMultiplier = () => {
    var mbtns = document.getElementsByClassName('rb-rm-btn');
    for (var i = 0; i < mbtns.length; i++) {
        mbtns[i].addEventListener("click", function () {
            var amount = parseFloat(this.getAttribute('data-multi'));
            multiplyAmounts(amount);
            unselectAllBtns(mbtns, this);
        });
    }
};

window.addEventListener("load", function () {
    recipeMultiplier();
});
