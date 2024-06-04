var prologue="";
prologue=prologue.replace(/\+/g,'%20');
prologue=unescape(prologue);
function LR_GetObj(id,theDoc){if(!theDoc){theDoc = document;}if (theDoc.getElementById){return theDoc.getElementById(id);}else if (document.all){return theDoc.all(id);}}if(LR_GetObj('LR_Operator_Div')!=null)
{
	LR_GetObj('LR_Operator_Div').innerHTML=prologue;
}
else
{
document.writeln(prologue)
}
