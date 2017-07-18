<?php/** * 用户管理 * * 操作： *      login：登录 *      register：注册 *      logout：退出 * * 消息处理： *      check_login：登录验证 *      check_register：注册验证 *      get_verify：获取验证码 * * @author xiaomo<xiaomo@nixiaomo.com> */namespace Home\Controller;class UserController extends BaseController{    /**     * 登录     */    public function login()    {        // 从cookie中读取用户名        if (cookie('login')) {            $loginData = explode('|', cookie('login'));            session("user", $loginData[0]);            session("user_id", $loginData[1]);            session("nickname", $loginData[2]);            $this->redirect('Index/index');        } else $this->display();    }    /**     * 消息处理——登录验证     */    public function check_login()    {        $user = I('post.user');        $psw = I('post.psw');        $code = I('post.code');        $remember = I('post.remember');        // 参数验证        $ajaxData['status'] = 0;        if (empty($user) || empty($psw) || empty($code) || empty($remember)) {            $ajaxData['info'] = '参数有误';            $this->ajaxReturn($ajaxData, 'JSON');        }        // 判断验证码        if (!$this->checkVerify($code)) {            $ajaxData['info'] = '验证码错误';            $this->ajaxReturn($ajaxData, 'JSON');        }        // 检查用户名密码        $where['user'] = array('eq', $user);        $msg = M('user')->field('id,nickname,psw')->where($where)->find();        if (empty($msg)) {            $ajaxData['info'] = '用户名不存在';        } else if ($msg['psw'] != $psw) {            $ajaxData['info'] = '密码不正确';        } else {    // 登录成功            // 保存cookie            if ($_POST['remember'] == "true") {                cookie('login', $user . '|' . $msg['id'] . '|' . $msg['nickname']);            }            session("user", $user);            session("user_id", $msg['id']);            session("nickname", $msg['nickname']);            // 登录成功跳转url            $redirectUrl = U('Index/index');            if (session('redirect_to')) {                $redirectUrl = session('redirect_to');                session('redirect_to', null);            }            $ajaxData['status'] = 1;            $ajaxData['info'] = '登录成功';            $ajaxData['redirect_to'] = $redirectUrl;        }        $this->ajaxReturn($ajaxData, 'JSON');    }    /**     * 消息处理——获取验证码     */    public function get_verify()    {        $Verify = new \Think\Verify();        $Verify->length = 4;    // 长度        $Verify->useCurve = false;  // 去除混淆曲线        $Verify->entry();    }    /**     * 注册     */    public function register()    {        $this->display();    }    /**     * 消息处理——注册验证     */    public function check_register()    {        $user = I('post.user');        $psw = I('post.psw');        $code = I('post.code');        // 参数验证        $ajaxData['status'] = 0;        if (empty($user) || empty($psw) || empty($code)) {            $ajaxData['info'] = '参数有误';            $this->ajaxReturn($ajaxData, 'JSON');        }        // 判断验证码        if (!$this->checkVerify($code)) {            $ajaxData['info'] = '验证码错误';            $this->ajaxReturn($ajaxData, 'JSON');        }        // 检查用户名是否存在        $where['user'] = array('eq', "$user");        $msg = M('user')->where($where)->find();        if (!empty($msg)) {            $ajaxData['info'] = '用户名已存在';        } else {            // 注册，验证数据            $data['user'] = $user;            $data['psw'] = $psw;            $User = D('user');            if (!$User->create($data)) {                $ajaxData['info'] = $User->getError();   // 返回错误状态                $this->ajaxReturn($ajaxData, 'JSON');            }            // 插入数据库            $result = $User->add($data);            if ($result > 0) {                $ajaxData['status'] = 1;                $ajaxData['info'] = '注册成功';            } else {                $ajaxData['info'] = '注册失败';            }        }        $this->ajaxReturn($ajaxData, 'JSON');    }    /**     * 退出     */    public function logout()    {        cookie(null);        session(null);        $this->redirect('User/login');    }}