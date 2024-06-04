<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>出来文件</title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link href="/Public/layui/css/layui.css" rel="stylesheet">
    <link href="/Public/layui/css/global.css" rel="stylesheet">
    <script src="/Public/home/js/jquery-3.2.1.min.js" type="text/javascript"></script>
    <script src="/Public/layui/layui.js" type="text/javascript"></script>
</head>
<body>
<style>
    .layui-main {
        padding-top: 50px !important;
    }

    .site-text {
        font-size: 18px;
    }

</style>

<div class="layui-main site-inline">
    <blockquote class="site-text layui-elem-quote">
        1.请把文件传入根目录下的<em>File</em>文件夹内，然后对应传入你的文件夹名<br>
        2.请把图片传入根目录下的<em>Img</em>文件夹内，然后对应传入你的文件夹名<br>
        3.字母区分大小写<br>
    </blockquote>
    <div class="site-title">
        <fieldset>
            <legend><a>操作：</a></legend>
        </fieldset>
    </div>
    <div class="site-content">
        <form class="layui-form" onclick="preventFormSubmit(event)">
            <div class="layui-form-item">
                <label class="layui-form-label">文件路径</label>
                <div class="layui-input-block">
                    <input autocomplete="off" class="layui-input" lay-verify="required" name="file" placeholder="请输入"
                           required type="text" value="">
                </div>

            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">图片路径</label>
                <div class="layui-input-block">
                    <input autocomplete="off" class="layui-input" lay-verify="required" name="img" placeholder="请输入"
                           required type="text" value="">
                </div>
            </div>

            <div class="layui-form-item">
                <div class="layui-input-block">
                    <button class="layui-btn"  lay-filter="formDemo" lay-submit="">立即提交</button>
<!--                    <button type="reset" class="layup-btn layui-btn-primary">重置</button>-->
                </div>
            </div>
        </form>
    </div>

    <table class="layui-table">
        <colgroup>
        </colgroup>
        <thead>
        <tr>
            <th style="text-align: center">信息</th>
        </thead>
        <tbody class="content">
        </tbody>
    </table>
</div>
<script>
    //Demo
    layui.use(['form', 'jquery', 'layer', 'flow'], function () {
        var form = layui.form;
        var $ = layui.jquery;
        var layer = layui.layer;
        var flow = layui.flow;

        //监听提交
        form.on('submit(formDemo)', function (data) {

            $(".layui-btn").attr("disabled",'disabled');

            flow.load({
                elem: '.content' //指定列表容器
                , isAuto: false
                , done: function (page, next) { //到达临界点（默认滚动触发），触发下一页
                    //以jQuery的Ajax请求为例，请求下一页数据（注意：page是从2开始返回）
                    $.ajax({
                        type: "POST",
                        url: "<?php echo U('api/addImgDo');?>",
                        /* dataType:'json',*/
                        data: data.field,
                        success: function (msg) {
                            if (msg.status === 1) {
                                $.each(msg.data, function (index, item) {
                                    $('.content').append('<tr><td>执行第' + item.id + '条成功：' + item.title + '</td></tr>');
                                });
                                layer.msg(msg.msg);
                                $(".layui-form")[0].reset();
                                layui.form.render();
                            } else {
                                layer.msg(msg.msg);
                            }
                            $(".layui-flow-more").attr('style', 'display:none');
                            // 在某个时刻关闭loading
                            // layer.close(index);
                            $(".layui-btn").removeAttr("disabled",'disabled');
                        }
                    });
                }
            });

        });
    });

    function preventFormSubmit(event) {
        event.preventDefault();

        // 这里可以添加你想要执行的代码
        // alert('表单不会被提交！');
    }

</script>
</body>
</html>