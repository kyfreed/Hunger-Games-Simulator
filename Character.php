<?php

class Character {

    public $name;
    public $nick;
    public $gender;
    public $disposition;
    public $strength;
    public $health;
    public $maxStrength;
    public $maxHealth;
    public $modifiedStrength;
    public $dexterity;
    public $intelligence;
    public $charisma;
    public $defense = 0;
    public $image;
    public $status = "Alive";
    public $actionTaken = "false";
    public $daysOfFood = 1;
    public $daysWithoutFood = 0;
    public $daysOfWater = 1;
    public $desiredItems = [];
    public $inventory = [];
    public $arrows = 0;
    public $explosivesPlanted = 0;
    public $memberOfAlliance = -1;
    public $equippedItem = "";
    public $kills = 0;
    public $daysAlive = 0;
    public $orderMarker;
    public $place = 0;
    public $poisonedDaysCounter = -1;
    public $typeOfPoison = "";

    function __construct($name, $nick, $gender, $disposition, $strength, $health, $dexterity, $intelligence, $charisma, $image, $orderMarker) {
        $this->name = htmlspecialchars($name);
        $this->nick = $nick;
        $this->gender = $gender;
        $this->disposition = $disposition;
        $this->strength = $strength;
        $this->maxStrength = $strength;
        $this->health = $health;
        $this->maxHealth = $health;
        $this->modifiedStrength = $strength / 5;
        $this->dexterity = $dexterity;
        $this->intelligence = $intelligence;
        $this->charisma = $charisma;
        $this->image = $image;
        $this->orderMarker = $orderMarker;
    }

    function snapshot() {
        var_dump(get_object_vars($this));
    }

    function die($cause, $place) {
        $character->status = "Dead";
        $character->place = $place;
        $character->killedBy = $cause;
    }

}
