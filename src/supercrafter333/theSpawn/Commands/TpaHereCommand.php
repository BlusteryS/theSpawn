<?php

namespace supercrafter333\theSpawn\Commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\Player;
use pocketmine\plugin\Plugin;
use supercrafter333\theSpawn\MsgMgr;
use supercrafter333\theSpawn\Others\TpaInfo;
use supercrafter333\theSpawn\theSpawn;

class TpaHereCommand extends Command implements PluginIdentifiableCommand
{

    private $pl;

    public function __construct(string $name, string $description = "", string $usageMessage = null, array $aliases = [])
    {
        $this->pl = theSpawn::getInstance();
        $this->setPermission("theSpawn.tpahere.cmd");
        parent::__construct($name, "Start a TPA to you.", "§4Usage: §r/tpahere <player>", $aliases);
    }


    /**
     * @param CommandSender $s
     * @param string $commandLabel
     * @param string[] $args
     * @return mixed
     */
    public function execute(CommandSender $s, string $commandLabel, array $args): void
    {
        $pl = $this->pl;
        if (!$s->hasPermission($this->getPermission())) {
            $s->sendMessage(MsgMgr::getNoPermMsg());
            return;
        }
        if (!$s instanceof Player) {
            $s->sendMessage(MsgMgr::getOnlyIGMsg());
            return;
        }
        if (count($args) < 1) {
            $s->sendMessage($this->usageMessage);
            return;
        }
        $target = $pl->getServer()->getPlayer($args[0]);
        if (!$target instanceof Player) {
            $s->sendMessage(str_replace("{name}", $args[0], theSpawn::$prefix . MsgMgr::getMsg("player-not-found")));
            return;
        }
        $name = $target->getName();
        if (!theSpawn::getInstance()->addTpa($s->getName(), $name, true)) {
            $s->sendMessage(theSpawn::$prefix . theSpawn::$prefix . MsgMgr::getMsg("pending-tpa-error"));
            return;
        }
        $tpa = new TpaInfo($s->getName());
        $tpa->runTask($pl->getCfg()->get("tpa-time"));
        $s->sendMessage(str_replace("{target}", $name, theSpawn::$prefix . MsgMgr::getMsg("tpa-send")));
        $target->sendMessage(str_replace("{source}", $s->getName(), theSpawn::$prefix . MsgMgr::getMsg("new-tpa-here")));
        return;
    }

    /**
     * @return Plugin
     */
    public function getPlugin(): Plugin
    {
        return $this->pl;
    }
}