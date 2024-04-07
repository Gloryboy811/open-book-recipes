
var muiltiplyAmounts = (multi) => {
    var mamounts = document.getElementsByClassName('rb-rm-amount');
    for (var i = 0; i < mamounts.length; i++) {
        var amount = mamounts[i].attributes['data-amount'].value;
        if (amount.length > 0) {
            mamounts[i].innerHTML = multiplyFullNumber(amount, multi);
        }
        
    }
}

var multiplyFullNumber = (numberString, multi) => {
    var parts = numberString.split(' ');
    var top = 0;
    var divisor = 1;
    
    for (var i = 0; i < parts.length; i++) {
        var part = parts[i];
        var [pTop, pBot] = splitFrac(part);

        if (multi > 1) {
            pTop = pTop * multi;
        } else {
            pBot = pBot / multi;
        }
        
        while (pBot > divisor) {
            divisor = divisor*2;
            top = top*2;
        }

        while (pBot < divisor) {
            pBot = pBot*2;
            pTop = pTop*2;
        }

        if (pBot != divisor) {
            console.log("Yeah this didn't work.", pBot, divisor);
        }

        top += pTop;        
    }

    var newNumber = toMixedFractionStr(top, divisor);
    return newNumber;
}



var splitFrac = (frac) => {
    var top = 0;
    var bottom = 1;

    var numbers = frac.split('/');
    if (numbers.length > 0) {
        top = parseInt(numbers[0]);
    } 
    if (numbers.length > 1) {
        bottom = parseInt(numbers[1]);
    } 

    return [top, bottom];
}

var toMixedFractionStr = (top, bottom) => {
    var wholeNumber = 0;
    while (top >= bottom) {
        wholeNumber++;
        top = top - bottom;
    }

    if (top == 0) {
        return wholeNumber;
    } 

    var niceFrac = strToFrac(`${top}/${bottom}`);

    if (wholeNumber == 0) {
        return niceFrac;
    }

    return `${wholeNumber} ${niceFrac}`;
}

var strToFrac = (strNum) => {
    var frac = ['½', '⅓','⅔','¼','¾','⅛','⅜','⅝','⅞'];
	var str = ['1/2', '1/3','2/3','1/4','3/4','1/8','3/8','5/8','7/8'];

    var index = str.indexOf(strNum);
    if (index == -1) return strNum;

    return frac[index];
}

var unselectAllBtns = (allmbtns, except) => {
    var multiVal = except.getAttribute('data-multi');
    for (var i = 0; i < allmbtns.length; i++) {
        var cur = allmbtns[i];
        cur.classList.remove('-selected');
        if (cur.getAttribute('data-multi') == multiVal) {
            cur.classList.add('-selected');
        }
    }
    except.classList.add('-selected');
}

var recipeMultiplier = () => {
    var mbtns = document.getElementsByClassName('rb-rm-btn');
    for (var i = 0; i < mbtns.length; i++) {
        mbtns[i].addEventListener("click", function (t) {
            var amount = parseFloat(this.attributes['data-multi'].value);
            muiltiplyAmounts(amount);
            unselectAllBtns(mbtns, this);
        });
    }
};  


window.addEventListener("load", function(){
    recipeMultiplier();
});
