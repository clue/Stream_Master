<?php

abstract class Stream_Master_Client{
    public static function factory($client){
        if(is_resource($client)){
            return new Stream_Master_Client_Stream($client);
        }else{
            return new Stream_Master_Client_Old($client);
        }
        return $client;
    }
    
    /**
     * get native client handle/resource/instance
     * 
     * @return mixed
     */
    public function getNative(){
        return $this;
    }
    
    /**
     * get readable stream resource for this client
     * 
     * @return resource|NULL
     */
    abstract public function getStreamRead();
    
    /**
     * get writable stream resource for this client
     * 
     * @return resource|NULL
     */
    abstract public function getStreamWrite();
    
    public function onCanRead($master){
        //$native = $this->getNative();
        try{
            $master->onClientRead($this);
        }
        catch(Stream_Master_Exception $e){
            $this->onClose($master);
        }
    }
    
    public function onCanWrite($master){
        try{
            $master->onClientWrite($this);
        }
        catch(Stream_Master_Exception $e){
            $this->onClose($master);
        }
    }
    
    public function onClose($master){
        $master->onClientClose($this);
        $this->close();
    }
    
    /**
     * close streams
     * 
     * @return Worker_Master_Client $this (chainable)
     * @uses Worker_Master_Client::getStreamRead()
     * @uses Worker_Master_Client::getStreamWrite()
     * @uses fclose()
     */
    public function close(){
        $r = $this->getStreamRead();
        $w = $this->getStreamWrite();

        if($r !== NULL){
            fclose($r);
        }
        if($w !== NULL && $w !== $r){
            fclose($w);
        }
        return $this;
    }
}
