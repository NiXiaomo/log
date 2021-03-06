<?php
/**
 * Comment模型
 *
 * @author xiaomo<i@nixiaomo.com>
 */

namespace Admin\Model;


use Think\Model;

class CommentModel extends Model
{
    protected $_validate = array(
        array('comment_author', '1,32', '昵称请保持在32个字符以内！', 0, 'length'),
        array('comment_author_email', '/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/', '邮箱格式有误！', 0, 'regex'),
        array('comment_author_url', '/[a-zA-Z0-9][-a-zA-Z0-9]{0,62}(\.[a-zA-Z0-9][-a-zA-Z0-9]{0,62})+\.?/', '网址格式有误！', 2, 'regex'),
    );
}