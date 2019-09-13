<?php

use Phalcon\Mvc\Model;

class BaseModel extends Model
{
    public function afterSave()
    {       
        echo '{"status": "success", "data": "Record saved."}';
    }

    public function notSaved()
    {       
        $msg = '';
        
        $messages = $this->getMessages();

        foreach ($messages as $message) {
            $msg .= $message;
        }

        $response = [ 
            'status' => 'failure',
            'data'   => $msg
        ];

        echo json_encode($response);
    }
}
