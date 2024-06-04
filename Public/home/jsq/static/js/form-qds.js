
var formId = ''
// 获取验证码
function getPhoneCode(id) {
    //点击按钮
    var _getCode = $(id).find('.get-code')
    var _tel = $(id).find('input[name="tel"]').val()
    var _reg = /^1[3,4,5,6,7,8,9]\d{9}$/;
    if (_tel == "") {
        layer.msg("请输入您的电话");
        return;
    }
    if (!_reg.test(_tel)) {
        layer.msg("请输入正确的手机号码");
        return;
    }
    var verificationCode = {
        customerPhone: doEncrypt(_tel),
        imageCode: "",
        whetherVerifyImageCode: false,
    }
    getCodeApi(verificationCode, _getCode)
}
var clickNum = 0
function consultForm(id, cq, cqc) {
    if (clickNum == 0) {
        clickNum = 1
        setTimeout(function () {
            clickNum = 0
        }, 3000)
        let _tel = $(id).find('input[name="tel"]').val()
        let _code = $(id).find('input[name="code"]').val()
        let name = $(id).find('input[name="name"]').val()
        let _dkje = $(id).find('input[name="dkje"]').val()
        var dkje = _dkje >= 1000 ? _dkje / 10000 : _dkje
        let city = $(id).find('.city-select option:selected').val()
        let _reg = /^1[3,4,5,6,7,8,9]\d{9}$/;
        if (city == "") {
            layer.msg("请选择所在的城市");
            return;
        }
        if (name == "") {
            layer.msg("请输入您的姓名");
            return;
        }
        if (dkje == "") {
            layer.msg("请输入贷款金额");
            return;
        }
        if (_tel == "") {
            layer.msg("请输入您的电话");
            return;
        }
        if (!_reg.test(_tel)) {
            layer.msg("请输入正确的手机号码");
            return;
        }
        if (!_code) {
            layer.msg('请输入验证码');
            return
        }
        formId = getDate() - _tel - Math.floor(Math.random() * 1000 + 10) + ''
        if (cq == '保单贷' || cq == '任我贷商业保单贷') {
            cqc = 'FL20211208186013'
        } else if (cq == '顶乐贷' || cq == '顶新贷' || cq == '房易贷') {
            cq = '房产贷'
            cqc = 'FL20211208186015'
        } else if (cq == '新易贷' || cq == '新一贷社保贷' || cq == '精英贷' || cq == '平安银行信用贷') {
            cq = '社保贷'
            cqc = 'FL20211208186011'
        } else if (cq == '新一贷工薪贷') {
            cq = '工薪贷'
            cqc = 'FL20211208186010'
        } else if (cq == '任我贷公积金贷' || cq == '平安融E贷') {
            cq = '公积金贷'
            cqc = 'FL20211208186012'
        } else if (cq == '生意贷') {
            cq = '经营贷'
            cqc = 'FL20211208186006'
        }
        var formMessage = {
            areaCode: city.split(',')[1],  // 城市code
            areaName: city.split(',')[0],  //城市名称
            customerName: name,  // 客户姓名
            customerPhone: doEncrypt(_tel),  // 客户手机号
            deviceType: "pc",  // 设备端口
            formId: formId,  // formID
            keyword: "",
            remark: "",  // 备注
            requireCode: [cqc], // 需求编码
            requireName: [cq], // 需求名称
            sourceUrl: window.location.href + "?utm_medium=seo&utm_source=bdpc",  // URL
            webType: "zytg",
            verificationCode: _code  // 验证码code
        }
        if (consultFormApi(formMessage) == 200) {
            $(id).find('input[name="tel"]').val('')
            $(id).find('input[name="dkje"]').val('')
            $(id).find('input[name="name"]').val('')
            $(id).find('input[name="code"]').val('')
            $(id).find('.check').removeClass('no')
            $(id).find(".city-select option:first").prop("selected", "selected")
            secondaryForm((city.split(',')[0]), dkje)
        }
    }
    else {
        layer.msg('请勿频繁操作！')
    }
}
// 关联二级表单
function secondaryForm(city, dkje) {
    var secondaryData = {
        city,
        dkje
    }
    var secondaryFormData = {
        dataJson: secondaryData,
        formId: formId
    }
    if (JSON.stringify(secondaryData) !== '{}') {
        secondaryFormData.dataJson = JSON.stringify(secondaryFormData.dataJson)
    } else {
        layer.msg("请至少选中一项")
        return
    }
    if (secondaryFormApi(secondaryFormData) == 200) {
    }
}