export default Array.prototype.Sum = function (prop) {
    var total = 0;
    for ( var i = 0, _len = this.length; i < _len; i++ ) {
        total += parseFloat(this[i][prop]);
    }
    return total;
}
