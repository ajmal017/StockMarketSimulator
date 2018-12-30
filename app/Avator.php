<?php

namespace App;

use Matriphe\Imageupload\ImageuploadModel;

class Avator extends ImageuploadModel
{
   protected $table = 'image_uploads';	

   public function investor(){
        return $this->belongsTo('App\Investor', 'belongsTo');
    }       
}
