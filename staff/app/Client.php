<?php

namespace App;

use DB;
use App\Models\ClientOption;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;

class Client extends Authenticatable
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $_headlineDimension;
    protected $_thumbDimension;
    protected $_oAuthSecretPair;

    protected $fillable = [
        'name', 'email', 'password'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function options()
    {
        return $this->hasOne('App\Models\ClientOption', 'client_id', 'id');
    }

    public function setHeadlineDimension($width, $height, $w_option_name = 'headline_width', $h_option_name = 'headline_height')
    {
        $hd_width = $this->options()->where('option', $w_option_name)->first();
        $hd_height = $this->options()->where('option', $h_option_name)->first();
        if(!($hd_width && $hd_height)) {
            $hd_width = new ClientOption();
            $hd_width->client_id = $this->id;
            $hd_width->option = $w_option_name;
            $hd_height = new ClientOption();
            $hd_height->client_id = $this->id;
            $hd_height->option = $h_option_name;
        }
        $hd_width->value = $width;
        $hd_height->value = $height;
        $hd_width->save();
        $hd_height->save();
    }

    public function setThumbDimension($width, $height)
    {
        $this->setHeadlineDimension($width, $height, 'thumb_width', 'thumb_height');
    }

    protected function getDimension($w_option_name, $h_option_name)
    {
        $hd_width = $this->options()->where('option', $w_option_name)->first();
        $hd_height = $this->options()->where('option', $h_option_name)->first();
        $result = new \stdClass();
        $result->width = $hd_width->value ? $hd_width->value : 1;
        $result->height = $hd_height->value ? $hd_height->value : 1;
        $result->ratio = number_format($result->width /  $result->height, 2, '.', ',');
        return $result;
    }

    public function getHeadlineDimension()
    {
        if($this->_headlineDimension) return $this->_headlineDimension;
        return $this->_headlineDimension = $this->getDimension('headline_width', 'headline_height');
    }

    public function getThumbDimension()
    {
        if($this->_thumbDimension) return $this->_thumbDimension;
        return $this->_thumbDimension = $this->getDimension('thumb_width', 'thumb_height');
    }

    public function isImageFitHeadline($width, $height)
    {
        $req = $this->getHeadlineDimension();
        return $this->isImageFit($req, $width, $height);
    }

    public function isImageFitThumb($width, $height)
    {
        $req = $this->getThumbDimension();
        return $this->isImageFit($req, $width, $height);
    }

    public function getOAuthSecretPair()
    {
        if($this->_oAuthSecretPair) return $this->_oAuthSecretPair;
        $this->_oAuthSecretPair = [
            $this->options()->where('option', 'oauth_id')->first()->value,
            $this->options()->where('option', 'oauth_secret')->first()->value
        ];
        return $this->_oAuthSecretPair;
    }

    public function generateOAuthSecret()
    {
        $id = str_random(10);
        $secret = str_random(10);
        DB::table('oauth_clients')->insert([
            [
                'id' => $id,
                'secret' => $secret,
                'name' => $this->name,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]
        ]);

        $id_opt = new ClientOption();
        $id_opt->client_id = $this->id;
        $id_opt->option = 'oauth_id';
        $id_opt->value = $id;
        $id_opt->save();

        $secret_opt = new ClientOption();
        $secret_opt->client_id = $this->id;
        $secret_opt->option = 'oauth_secret';
        $secret_opt->value = $secret;
        $secret_opt->save();
    }

    protected function isImageFit($req, $width, $height)
    {
        if($req->width > $width || $req->height > $height) return false;
        if($req->ratio != number_format($width / $height, 2, '.', ',')) return false;
        return true;
    }
}
