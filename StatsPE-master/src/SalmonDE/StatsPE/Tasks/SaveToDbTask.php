<?php
namespace SalmonDE\StatsPE\Tasks;

use pocketmine\Server;

class SaveToDbTask extends \pocketmine\scheduler\AsyncTask
{

    private $credentials;
    private $changes;
    private $provider;

    public function __construct(array $credentials, array $changes, \SalmonDE\StatsPE\Providers\MySQLProvider $provider){
        $this->credentials = $credentials;
        $this->changes = $changes;
        $this->provider = $provider;
    }

    public function onRun(){
        $host = explode(':', $this->credentials['host']);
        $hostAddress = $host[0];
        $port = isset($host[1]) ? $host[1] : 3306;

        @$db = new \mysqli($hostAddress, $this->credentials['username'], $this->credentials['password'], $this->credentials['database'], $port);

        $query = '';
        foreach($this->changes as $playerName => $changedData){
            foreach($changedData as $entryName => $data){
                if($data['isIncrement']){
                    $query .= 'UPDATE StatsPE SET '.$entryName.' = '.$entryName.' + '.$data['value'].' WHERE Username='."'".$playerName."'".'; ';
                }else{
                    $data['value'] = \SalmonDE\StatsPE\Utils::convertValueSave($this->provider->getEntry($entryName), $data['value']);
                    $query .= 'UPDATE StatsPE SET '.$entryName.' = '.(is_string($data['value']) ? "'".$db->real_escape_string($data['value'])."'" : $data['value']).' WHERE Username='."'".$playerName."'".'; ';
                }
            }
        }

        $i = 0;

        if($db->multi_query($query)){
            do{
                $db->next_result();

                $i++;
            }while($db->more_results());
        }

        if($db->errno){
            $this->setResult('Error: '.$db->error.PHP_EOL.'Query: '.explode(';', $query)[$i]);
        }

        $db->close();
    }

    public function onCompletion(Server $server){
        if($this->getResult() !== null){
            if(($plugin = $server->getInstance()->getPluginManager()->getPlugin('StatsPE'))->isEnabled()){
                $plugin->getLogger()->error($this->getResult());
            }
        }
    }
}
