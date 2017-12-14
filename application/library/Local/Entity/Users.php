<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2017/12/14
 * Time: 23:56
 */

namespace Local\Entity;

/**
 * @Entity
 * @Table(name="users")
 */
class Users
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
     * @Column(type="string", length=255)
     */
    private $name;
    /**
     * 邮箱
     * @Column(type="string", length=255)
     */
    private $email;
    /**
     * 密码
     * @Column(type="string", length=255)
     */
    private $password;
    /**
     * TOKEN
     * @Column(type="string", length=100, columnDefinition="varchar(100) not null default ''")
     */
    private $remember_token;
    /**
     * 创建时间(可用integer)
     * @Column(type="datetime")
     */
    private $created_at;
    /**
     * 更新时间(可用integer)
     * @Column(type="datetime")
     */
    private $updated_at;


    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getEmail(){
        return $this->email;
    }

    public function setEmail($email){
        $this->email = $email;
    }
}
