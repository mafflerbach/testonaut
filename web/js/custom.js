$(document).ready(function () {
    if ($('#textBox').length > 0) {
        initDoc();
    }

    $("form[name='compForm']").submit(function (e) {
        var content;
        if (!$('#switchBox').prop('checked')) {
            content = $('#textBox').html();
        } else {
            content = $('#sourceText').text();
        }
        $("input[name='content']").attr('value', content);
    });




})


function initDoc() {
    oDoc = document.getElementById("textBox"), sDefTxt = oDoc.innerHTML, document.compForm.switchMode.checked && setDocMode(!0)
}
function formatDoc(o, e) {
    validateMode() && (document.execCommand(o, !1, e), oDoc.focus())
}
function SaveTextArea() {
    window.location = "data:application/octet-stream," + escape(textBox.innerHTML)
}
function validateMode() {
    return document.compForm.switchMode.checked ? (alert('Uncheck "Show HTML".'), oDoc.focus(), !1) : !0
}
function setDocMode(o) {
    var e;
    if (o) {
        e = document.createTextNode(oDoc.innerHTML), oDoc.innerHTML = "";
        var t = document.createElement("pre");
        oDoc.contentEditable = !1, t.id = "sourceText", t.contentEditable = !0, t.appendChild(e), oDoc.appendChild(t)
    } else document.all ? oDoc.innerHTML = oDoc.innerText : (e = document.createRange(), e.selectNodeContents(oDoc.firstChild), oDoc.innerHTML = e.toString()), oDoc.contentEditable = !0;
    oDoc.focus()
}
function printDoc() {
    if (validateMode()) {
        var o = window.open("", "_blank", "width=450,height=470,left=400,top=100,menubar=yes,toolbar=no,location=no,scrollbars=yes");
        o.document.open(), o.document.write('<!doctype html><html><head><title>Print</title></head><body onload="print();">' + oDoc.innerHTML + "</body></html>"), o.document.close()
    }
}
var oDoc, sDefTxt;



