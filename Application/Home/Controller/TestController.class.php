<?php
/**
 * @var
 *  修改文件
 */

namespace Home\Controller;

use Think\Controller;

class TestController extends Controller
{
//         "http://tool.cc/index.php/home/api/addimg";
    public function addImg()
    {

        $_POST['file']='qh';//内容文件
        $_POST['img'] = 'qh';//图片文件


        $lj = './Files/'.$_POST['file'].'/';
        $files = scandir( $lj ); // 使用'.'来获取当前目录下的文件和子目录

        $img_url = './Img/'.$_POST['img'].'/';
        $img_urls = '/Img/'.$_POST['img'].'/';
        $files_img = scandir( $img_url );
        $img = [];
        foreach ($files_img as $key => $file) {
            if ($file != "." && $file != "..") { // 排除当前目录和上级目录的引用
                $img[$key]['img'] = $img_urls . $file;
            }
        }

        $data = [];
        $img_count = count($img);
        foreach ($files as $key => $file) {
//            if ($file != "." && $file != ".."  && $key <=2) { // 排除当前目录和上级目录的引用
            if ($file != "." && $file != "..") { // 排除当前目录和上级目录的引用
                $index = $key % $img_count;
                $title = pathinfo( $file, PATHINFO_FILENAME );
                $data[$key]['img'] = $img[$index]['img'];
                $data[$key]['title']=$title;
                $data[$key]['id']=$key;
                $content = file_get_contents( $lj . $file );
                $content = $this->addImageAfterMiddleP($content, $data[$key]['img']);


                $filePath = $lj.$title.".txt"; // 文件路径
                // 打开文件进行读取和写入
                $edit_file = fopen($filePath, 'r+') or die('无法打开文件');
                // 读取文件内容
                $contentC = fread($edit_file, filesize($filePath));
                // 编辑内容
                $editedContent = $content;
                // 将新内容从开头开始写入，这将覆盖现有内容
                ftruncate($edit_file, 0); // 将文件截断到给定长度
                rewind($edit_file); // 将文件指针移动到开头
                fwrite($edit_file, $editedContent); // 写入新内容
                // 关闭文件
                fclose($edit_file);
//                echo "文件已保存".$key ."<br>";
                $data[$key]['id']=$key-1;

            }
        }
        dump($data);

    }


   public  function addImageAfterMiddleP($html, $image) {
        // 统计<p>标签出现的次数
        $count = substr_count($html, '<p>');
        // 找到最中间的<p>标签
        $middleIndex = ceil($count / 2);
        // 使用正则表达式匹配<p>标签
        $pattern = '<p>';
        preg_match_all($pattern, $html, $matches);
        // 在最中间的<p>标签后面加入图片
        $imageTag = '<img src="' . $image . '" alt="Image"';
        $newHtml = preg_replace_callback($pattern, function($match) use ($imageTag, $middleIndex) {
            static $count = 1;
            if ($count == $middleIndex) {
                $count++;
                return $match[0].'>'.$imageTag;
            } else {
                $count++;
                return $match[0];
            }
        }, $html);
        return $newHtml;
    }


    public function addInfo()
    {


//isgood tinyint(1) 是否推荐 1为推荐，0为不推荐
//firsttitle tinyint(1) 是否头条 1为头条，0为普通信息


//$jsonString = '{
//    "title": "谭咏麟演唱会- 流行天王再度唱响经典",
//    "ftitle": "谭咏麟演唱会- 流行天王再度唱响经典",
//    "newstext": "\n<h2>谭咏麟演唱会概述<\/h2>\n<p>谭咏麟演唱会是一场独具魅力的音乐盛会，以其精湛的唱功和精彩的表演风格，吸引了无数乐迷和音乐爱好者的关注。作为华语乐坛的流行天王，谭咏麟以其独特的音色和多才多艺的演唱风格赢得了众多粉丝的喜爱。<\/p>\n<p>谭咏麟是一个极具影响力的艺人，他的歌曲和舞台表演都让人难以忘怀。他的演唱会以其高品质和令人惊叹的舞台制作而闻名，每一场演唱会都成为粉丝们追逐的焦点。谭咏麟不仅能轻松驾驭各种风格的音乐，还能在演唱会上呈现出无与伦比的舞台表演，将观众们带入一个音乐和视觉的盛宴。<\/p>\n\n<h2>谭咏麟演唱会的音乐魅力<\/h2>\n<p>谭咏麟的演唱会涵盖了多种音乐风格，包括流行、摇滚、R&B和爵士等。他的歌曲旋律动人，歌词意境深远，既能引起听众的共鸣，也能让人沉浸在其中。不管是轻快欢快的流行曲还是深情款款的情歌，谭咏麟都能以其独特的演绎将每首歌曲都带入到一个全新的境界。<\/p>\n<p>此外，谭咏麟还以多样的舞台表演形式给观众带来惊喜。他的舞蹈和动作十分精准，身体语言充满张力，能够很好地传达歌曲的情感。此外，谭咏麟还善于与观众互动，他的幽默和性格魅力常常能够引发观众的欢笑和共鸣。<\/p>\n\n<h2>谭咏麟演唱会的精彩瞬间<\/h2>\n<p>谭咏麟的演唱会总是充满了惊喜和精彩的瞬间。他会在舞台上演唱他的经典歌曲，如《朋友》、《夜曲》和《千千阙歌》等。这些歌曲不仅让人产生共鸣，也成为了他的代表作品。此外，他还会演唱一些乐迷耳熟能详的翻唱曲目，如《月亮代表我的心》和《情人的眼泪》等。<\/p>\n<p>除了演唱歌曲，谭咏麟的演唱会还包含了精心设计的舞台效果和灯光秀。他经常会有精彩的变装，为观众表演不同的角色，将整个演唱会变成一个视觉和听觉的奇幻世界。他的演唱会也会加入一些特别的元素，如舞蹈、魔术和杂技等，为观众们呈现出一场视听盛宴。<\/p>\n\n<h2>谭咏麟演唱会的意义<\/h2>\n<p>谭咏麟的演唱会不仅是一场音乐盛会，也是一次感动人心的音乐之旅。他的歌曲不仅给观众带来无尽的欢笑和感动，也能够在这个瞬间让人回忆起过去的美好时光。他的演唱会成为了人们共同的记忆和情感纽带，让大家齐聚一堂，沉浸在音乐的海洋中。<\/p>\n<p>总之，谭咏麟演唱会是一场精彩绝伦的音乐盛宴，是流行天王再度唱响经典的舞台。他的音乐魅力、精彩瞬间和深远意义使得谭咏麟的演唱会成为乐迷和音乐爱好者们不可错过的盛会。<\/p>\n\n<p>感谢您阅读本文，希望通过这篇文章带给您关于谭咏麟演唱会的全新了解和分享。如果您对谭咏麟的音乐产生了兴趣，不妨去现场感受他的震撼演绎，相信会给您留下难忘的回忆。<\/p>\n",
//    "befrom": "zzk",
//    "writer": "http://www.sankewang.com/",
//    "classid": 1,
//    'keyboard':"",
//    "username": "kaifamei",
//    "token":"yyzn_spider_token",
//    "checked ":"1",
//    "isgood  ":"1",
//    "firsttitle  ":"1"
//
//}';
        $jsonString = '{
                        "keyboard":"",
                        "befrom":"http://www.sankewang.com/",
                        "username":"admin",
                        "token":"yyzn_spider_token",
                        "writer":"zzk",
                        "ftitle":"成都到贡嘎雪山：一个不可错过的壮丽之旅",
                        "classid":"15",
                        "keyboard":"成都到贡嘎雪山：一个不可错过的壮丽之旅",
                        "title":"成都到贡嘎雪山：一个不可错过的壮丽之旅",
                        "isgood  ":"1",
                        "firsttitle  ":"1",
                        "newstext":" <h2>成都到贡嘎雪山：一个不可错过的壮丽之旅</h2> <p>贡嘎雪山位于四川省雅江县，是中国四川最美的雪山之一，也是四川贡嘎山脉的主峰，海拔7556米。这座雄伟的雪山给人留下深刻的印象，吸引着世界各地的旅行者。对于那些想要追寻大自然、挑战自我并欣赏壮丽景色的人来说，成都到贡嘎雪山绝对是一个不可错过的目的地。</p> <p>从成都到贡嘎雪山有多种方式可以选择。一种选择是乘坐飞机。你可以从成都直接搭乘飞往稻城亚丁机场的航班，然后再从稻城亚丁乘坐私家车或者包车前往贡嘎雪山。这种方式虽然方便快捷，但是价格较高。</p> <p>另一种选择是驾车自驾游。从成都出发，你可以自驾前往雅安，然后通过康定，最后到达贡嘎雪山。这条路线全程大约需要8至10小时的驾车时间。虽然路途较远，但是你可以欣赏到沿途美丽的风景，例如康定草原、雅砻江大峡谷等。此外，自驾游还能更好地掌控旅行时间和路线，更加自由自在。</p> <p>当你到达贡嘎雪山后，你可以选择徒步登山或者乘坐缆车到达山顶。贡嘎雪山的登山路线有多条选择，包括贡嘎东峰和贡嘎西峰。登上贡嘎雪山后，你将欣赏到壮丽的雪山景色和周边的风景。同时，贡嘎雪山还是一个登山爱好者的天堂，每年都吸引着大量的登山爱好者前来挑战自我。</p> <p>除了雄伟的贡嘎雪山，贡嘎地区还有其他值得一游的景点。例如，贡嘎雪山脚下的雅江谷地拥有丰富的自然景观，包括雅江、嘎岗河、聂拉木羌寨等。你可以在这里欣赏到独特的藏族风情和自然美景。</p> <p>在行程计划上，你可以选择在贡嘎雪山附近的旅馆或者帐篷露营。这样可以更好地享受大自然的美景，同时也能更好地参与户外活动，如徒步、拍摄风景照片等。</p> <p>总而言之，成都到贡嘎雪山之旅是一个壮丽的旅程，为你带来难忘的回忆和独特的体验。你将有机会欣赏到雄伟的贡嘎雪山、体验自驾游的乐趣、参与登山挑战以及领略到贡嘎地区的丰富风情。不管你是喜欢户外探险还是享受大自然的美景，成都到贡嘎雪山都将是一个不容错过的目的地。</p> "
                        }';
        $_POST = json_decode( $jsonString, true );


        $a = http_post( "https://www.wanshiruyi.net/yyznApi.php", $_POST );

        dump( $a );

    }


}