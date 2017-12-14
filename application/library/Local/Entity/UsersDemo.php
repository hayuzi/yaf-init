<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2017/12/15
 * Time: 0:08
 */

namespace Local\Entity;

/**
 * @Entity
 * @Table(name="users_demo")
 */
class UsersDemo
{
    /**
     * 声明此字段为主键字段，自增及使用int类型
     * @Id
     * @Column(type="integer")
     * @GeneratedValue
     */
    private $id;
    /**
     * 用户名
     * @Column(type="string", length=36)
     */
    private $username;
    /**
     * 密码(32位char类型)
     * @Column(type="string", columnDefinition="char(32) not null default '' ")
     */
    private $password;
    /**
     * 手机号码（11位char类型，唯一索引）
     * @Column(type="string",  unique=true, columnDefinition="char(11) not null default '' ")
     */
    private $mobile;
    /**
     * 性别
     * @Column(type="boolean")
     */
    private $gender;
    /**
     * 出生日期
     * @Column(type="date")
     */
    private $birthday;
    /**
     * 积分
     * @Column(type="integer")
     */
    private $integral;
    /**
     * 最后登录时间(可用integer)
     * @Column(type="datetime")
     */
    private $last_login;
    /**
     * 余额
     * @Column(type="decimal", precision=10, scale=2)
     */
    private $balance;
}