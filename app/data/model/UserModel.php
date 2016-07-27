<?php
namespace ITECH\Data\Model;

class UserModel extends \ITECH\Data\Model\BaseModel
{
    public $id;
    public $username;
    public $password;
    public $name;
    public $firstname;
    public $slug;
    public $gender;
    public $birthday;
    public $address;
    public $province_id;
    public $district_id;
    public $email;
    public $phone;
    public $avatar_image;
    public $cover_image;
    public $job_title;
    public $job_title_eng;
    public $experience;
    public $experience_eng;
    public $description;
    public $description_eng;
    public $save_home;
    public $save_search;
    public $purchased_properties;
    public $newsletter;
    public $type;
    public $membership;
    public $is_verified;
    public $status;
    public $created_at;
    public $updated_at;
    public $logined_at;

    public function initialize()
    {
        parent::initialize();
        $this->setSource('land_user');

        $this->belongsTo('province_id', 'ITECH\Data\Model\LocationModel', 'id', array(
            'alias' => 'Province',
            'reusable' => true
        ));

        $this->belongsTo('district_id', 'ITECH\Data\Model\LocationModel', 'id', array(
            'alias' => 'District',
            'reusable' => true
        ));
    }

    public function validation()
    {
        $this->validate(new \Phalcon\Mvc\Model\Validator\Uniqueness(array(
            'field' => 'username',
            'message' => 'DUPLICATED_USERNAME'
        )));

        if ($this->validationHasFailed()) {
            return false;
        }

        return true;
    }
}