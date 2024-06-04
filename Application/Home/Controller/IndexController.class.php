<?phpnamespace Home\Controller;use Think\Controller;use Think\Page;class IndexController extends BaseController {    // 首页方法    public function index()    {        // 获取分类为1的文章列表        $where['is_del'] = 0;        $where['is_show'] = 1;        $article = M("article");        $banner = M("banner");        $banner_list = $banner->where($where)->order("sort desc")->select();        $list_one = $article->where($where)->where("cate_id = 1 and is_tui = 1")->order("addtime desc")->limit(15)->select();        // 获取分类为2的文章列表        $list_two = $article->where($where)->where("cate_id = 2 and is_tui = 1")->order("addtime desc")->limit(15)->select();        // 获取ID为3的SEO信息        $seo = $this->seo_info(3);        $dk_list = M("dk_list");        $where['id'] = array('in','352,356,358,360');        $dk_info =  $dk_list->where($where)->select();        // 将获取的数据赋值给模板变量        $this->assign(array(            "seo" => $seo,            "list_one" => $list_one,            "list_two" => $list_two,            "banner_list" => $banner_list,            "dk_info" => $dk_info,            "num" => substr(date("YmdHis", time()), 3, 5),        ));        // 渲染首页模板        $this->display();    }    public function test() {// 渲染首页模板        $this->display();    }    // dk方法    public function dk()    {        $this->extracted();    }    // dk_web方法    public function dk_web()    {        $this->extracted();    }    // dk_info方法    public function dk_info()    {        $this->web_extracted();    }    // dk_web_info方法    public function dk_web_info()    {        $this->web_extracted();    }    // gw方法    public function gw()    {        // 获取ID为5的SEO信息        $seo = $this->seo_info(5);        // 将获取的SEO信息赋值给模板变量        $this->assign("seo", $seo);        // 渲染gw视图模板        $this->display();    }    // gw_web方法    public function gw_web()    {        // 获取ID为5的SEO信息        $seo = $this->seo_info(5);        // 将获取的SEO信息赋值给模板变量        $this->assign("seo", $seo);        // 渲染gw_web视图模板        $this->display();    }    // news方法    public function news()    {        dump(I(''));        die;        // 获取菜单配置信息        $config = M("menu");        $map['is_del'] = 0;        $map['is_show'] = 1;        $map['f_id'] = -1;        $menu = $config->where($map)->order("sort desc")->select();        $this->assign("menu_son", $menu);        // 获取文章信息        $article = M("article");        $where = ['is_del' => 0];        // 获取热门文章列表        $hot_list = $article->where($where)->limit(10)->order("addtime desc")->select();        $this->assign("hot_list", $hot_list);        // 获取分类ID        $cate_id = I("cate_id");        if ($cate_id) {            $where['cate_id'] = $cate_id;        } else {            $where['cate_id'] = $menu[0]['id'];        }        $this->assign("cate_id", $where['cate_id']);        // 获取热门前3篇文章        $hot_3 = $article->where($where)->limit(3)->order("views desc")->select();        $this->assign("hot_3", $hot_3);        // 统计符合条件的文章数量        $count = $article->where($where)->count();        // 创建分页对象        $page = $this->showPage($count, 10);        $this->assign('page', $page->show());        // 获取文章列表        $list = $article->where($where)->order("addtime desc")->limit($page->firstRow . ',' . $page->listRows)->select();        // 获取ID为6的SEO信息        $seo = $this->seo_info($where['cate_id']);        // 将获取的数据赋值给模板变量        $this->assign("seo", $seo);        $this->assign("list", $list);        // 渲染news视图模板        $this->display();    }    // about方法    public function about()    {        // 获取ID为7的SEO信息        $seo = $this->seo_info(7);        // 将获取的SEO信息赋值给模板变量        $this->assign("seo", $seo);        // 渲染about视图模板        $this->display();    }    // qs方法    public function qs()    {        // 获取ID为8的SEO信息        $seo = $this->seo_info(8);        // 将获取的SEO信息赋值给模板变量        $this->assign("seo", $seo);        // 渲染qs视图模板        $this->display();    }    // details方法    public function details($id)    {        // 获取文章信息        $article = M("article");        // 根据ID查询单篇文章信息        $info = $article->where("id = $id")->find();        // 获取文章的SEO信息        $seo['seo_title'] = $info['seo_title'] ?: $info['title'];        $seo['seo_keyword'] = $info['seo_keyword']?: $info['title'];        $seo['seo_desc'] = $info['seo_desc']?: $info['title'];        // 设置筛选条件        $where['is_del'] = 0;        $where['is_show'] = 1;        $where['cate_id'] = $info['cate_id'];        // 获取前一篇文章和后一篇文章        $prev = $article->where("id < $id")->where($where)->order("id DESC")->find();        $next = $article->where("id > $id")->where($where)->order("id ASC")->find();        // 增加文章浏览量        $article->where("id = $id")->setInc('views');        // 将获取的数据赋值给模板变量        $this->assign("prev", $prev);        $this->assign("next", $next);        $this->assign("info", $info);        $this->assign("seo", $seo);        $this->assign("img_range", img_range());        // 渲染details视图模板        $this->display();    }    // 获取指定ID的SEO信息    protected function seo_info($id)    {        $menu = M("menu");        return $menu->where("id = $id")->find();    }    // extracted方法    public function extracted()    {        // 获取ID参数        $id = I("id");        // 获取表dk_type和dk_list的数据        $dk_type = M("dk_type");        $dk_list = M("dk_list");        $where['is_del'] = 0;        $where['is_show'] = 1;        // 获取dk_type的菜单信息        $dk_menu = $dk_type->where($where)->select();        // 根据分类ID获取dk_list的列表信息        $dk_list = $dk_list->where($where)->where("cate_id= $id")->select();        // 获取ID为4的SEO信息        $seo = $this->seo_info(4);        // 将获取的数据赋值给模板变量        $this->assign(array(            "seo" => $seo,            "cate_id" => $id,            "dk_menu" => $dk_menu,            "dk_list" => $dk_list        ));        // 渲染模板        $this->display();    }    // 获取分页对象    public function showPage($count, $pagesize = 10)    {        // 创建分页对象        $page = new \Think\PageHome($count, $pagesize);        // 配置分页样式        $page->setConfig('header', '<span>共 %TOTAL_ROW% 条</span>');        $page->setConfig('prev', '上一页');        $page->setConfig('next', '下一页');        $page->setConfig('last', '末页');        $page->setConfig('first', '首页');        $page->setConfig('theme', '%FIRST%%UP_PAGE%%LINK_PAGE%%DOWN_PAGE%%END%%HEADER%');        $page->lastSuffix = false;        return $page;    }    // web_extracted方法    public function web_extracted()    {        // 获取ID参数        $id = I("id");        // 获取dk_list的数据        $dk_list = M("dk_list");        $where['is_del'] = 0;        $where['is_show'] = 1;        // 根据ID查询单个dk_list的信息        $info = $dk_list->where($where)->where("id= $id")->find();        // 将字符串按分号分割成数组        $info['map'] = explode(";", $info['map']);        $info['info'] = explode(";", $info['info']);        // 获取ID为4的SEO信息        $seo = $this->seo_info(4);        // 将获取的数据赋值给模板变量        $this->assign(array(            "seo" => $seo,            "info" => $info        ));        //推荐热门        $article =  M("article");        $hot_article_a =$article->where($where)->where("is_rank = 1 and cate_id =1 ")->order("views desc")->limit(10)->select();        $this->assign("hot_article_a", $hot_article_a);        $hot_article_b =$article->where($where)->where("is_rank = 1 and cate_id =2 ")->order("views desc")->limit(10)->select();        $this->assign("hot_article_b", $hot_article_b);        // 渲染模板        $this->display();    }    public function xieyi(){        // 渲染模板        $this->display();    }    public function jsq(){        $this->display();    }    public function jsq2(){        $this->display();    }    public function jsq3(){        $this->display();    }    public function jsq4(){        $this->display();    }    public function jsq5(){        $this->display();    }    public function jsq6(){        $this->display();    }    public function jsq_link(){        $this->display();    }}