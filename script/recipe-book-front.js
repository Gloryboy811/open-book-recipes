
var muiltiplyAmounts = (multi) => {
    var mamounts = document.getElementsByClassName('rb-rm-amount');
    for (var i = 0; i < mamounts.length; i++) {
        var amount = parseFloat(mamounts[i].attributes['data-amount'].value);
        mamounts[i].innerHTML = amount * multi;
    }
}

var unselectAllBtns = (allmbtns, except) => {
    for (var i = 0; i < allmbtns.length; i++) {
        allmbtns[i].classList.remove('-selected');
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
    console.log("Multiplier Buttons Primed!");
};  

window.addEventListener("load", function(){
    recipeMultiplier();
});
