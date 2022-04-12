<?php

namespace App\Entity;

use App\Repository\TanksStatsRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=TanksStatsRepository::class)
 * @ORM\HasLifecycleCallbacks
 */
class TanksStats extends EntityBase
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Groups({"api_data", "player_details", "recently_played"})
     * @ORM\ManyToOne(targetEntity=Tank::class, inversedBy="tanksStats")
     * @ORM\JoinColumn(nullable=false)
     */
    private $tank;

    /**
     * @Groups({"api_data", "player_details"})
     * @ORM\Column(type="integer", nullable=true)
     */
    private $spotted;

    /**
     * @Groups({"api_data", "player_details"})
     * @ORM\Column(type="integer", nullable=true)
     */
    private $piercings_received;

    /**
     * @Groups({"api_data", "player_details"})
     * @ORM\Column(type="integer", nullable=true)
     */
    private $hits;

    /**
     * @Groups({"api_data", "player_details"})
     * @ORM\Column(type="integer", nullable=true)
     */
    private $damage_assisted_track;

    /**
     * @Groups({"api_data", "player_details"})
     * @ORM\Column(type="integer", nullable=true)
     */
    private $wins;

    /**
     * @Groups({"api_data", "player_details"})
     * @ORM\Column(type="integer", nullable=true)
     */
    private $losses;

    /**
     * @Groups({"api_data", "player_details"})
     * @ORM\Column(type="integer", nullable=true)
     */
    private $no_damage_direct_hits_received;

    /**
     * @Groups({"api_data", "player_details"})
     * @ORM\Column(type="integer", nullable=true)
     */
    private $capture_points;

    /**
     * @Groups({"api_data", "player_details"})
     * @ORM\Column(type="integer", nullable=true)
     */
    private $battles;

    /**
     * @Groups({"api_data", "player_details"})
     * @ORM\Column(type="integer", nullable=true)
     */
    private $damage_dealt;

    /**
     * @Groups({"api_data", "player_details"})
     * @ORM\Column(type="integer", nullable=true)
     */
    private $explosion_hits;

    /**
     * @Groups({"api_data", "player_details"})
     * @ORM\Column(type="integer", nullable=true)
     */
    private $damage_received;

    /**
     * @Groups({"api_data", "player_details"})
     * @ORM\Column(type="integer", nullable=true)
     */
    private $piercings;

    /**
     * @Groups({"api_data", "player_details"})
     * @ORM\Column(type="integer", nullable=true)
     */
    private $shots;

    /**
     * @Groups({"api_data", "player_details"})
     * @ORM\Column(type="integer", nullable=true)
     */
    private $explosion_hits_received;

    /**
     * @Groups({"api_data", "player_details"})
     * @ORM\Column(type="integer", nullable=true)
     */
    private $damage_assisted_radio;

    /**
     * @Groups({"api_data", "player_details"})
     * @ORM\Column(type="integer", nullable=true)
     */
    private $xp;

    /**
     * @Groups({"api_data", "player_details"})
     * @ORM\Column(type="integer", nullable=true)
     */
    private $direct_hits_received;

    /**
     * @Groups({"api_data", "player_details"})
     * @ORM\Column(type="integer", nullable=true)
     */
    private $frags;

    /**
     * @Groups({"api_data", "player_details"})
     * @ORM\Column(type="integer", nullable=true)
     */
    private $survived_battles;

    /**
     * @Groups({"api_data", "player_details"})
     * @ORM\Column(type="integer", nullable=true)
     */
    private $dropped_capture_points;

    /**
     * @Groups({"api_data", "player_details"})
     * @ORM\Column(type="string", length=20)
     */
    private $last_battle_time;

    /**
     * @Groups({"api_data", "player_details"})
     * @ORM\Column(type="integer", nullable=true)
     */
    private $max_xp;

    /**
     * @Groups({"api_data", "player_details"})
     * @ORM\Column(type="integer", nullable=true)
     */
    private $trees_cut;

    /**
     * @Groups({"api_data", "player_details"})
     * @ORM\Column(type="integer", nullable=true)
     */
    private $max_frags;

    /**
     * @Groups({"api_data", "player_details"})
     * @ORM\Column(type="integer", nullable=true)
     */
    private $mark_of_mastery;

    /**
     * @Groups({"api_data", "player_details"})
     * @ORM\Column(type="integer", nullable=true)
     */
    private $battle_life_time;

    /**
     * @ORM\ManyToOne(targetEntity=Player::class, inversedBy="tanksStats")
     * @ORM\JoinColumn(nullable=false)
     */
    private $player;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTank(): ?Tank
    {
        return $this->tank;
    }

    public function setTank(?Tank $tank): self
    {
        $this->tank = $tank;

        return $this;
    }

    public function getSpotted(): ?int
    {
        return $this->spotted;
    }

    public function setSpotted(?int $spotted): self
    {
        $this->spotted = $spotted;

        return $this;
    }

    public function getPiercingsReceived(): ?int
    {
        return $this->piercings_received;
    }

    public function setPiercingsReceived(?int $piercings_received): self
    {
        $this->piercings_received = $piercings_received;

        return $this;
    }

    public function getHits(): ?int
    {
        return $this->hits;
    }

    public function setHits(?int $hits): self
    {
        $this->hits = $hits;

        return $this;
    }

    public function getWins(): ?int
    {
        return $this->wins;
    }

    public function setWins(?int $wins): self
    {
        $this->wins = $wins;

        return $this;
    }

    public function getLosses(): ?int
    {
        return $this->losses;
    }

    public function setLosses(?int $losses): self
    {
        $this->losses = $losses;

        return $this;
    }

    public function getNoDamageDirectHitsReceived(): ?int
    {
        return $this->no_damage_direct_hits_received;
    }

    public function setNoDamageDirectHitsReceived(?int $no_damage_direct_hits_received): self
    {
        $this->no_damage_direct_hits_received = $no_damage_direct_hits_received;

        return $this;
    }

    public function getCapturePoints(): ?int
    {
        return $this->capture_points;
    }

    public function setCapturePoints(?int $capture_points): self
    {
        $this->capture_points = $capture_points;

        return $this;
    }

    public function getBattles(): ?int
    {
        return $this->battles;
    }

    public function setBattles(?int $battles): self
    {
        $this->battles = $battles;

        return $this;
    }

    public function getDamageSummary(): ?int
    {
        return $this->getDamageDealt() + $this->getDamageAssistedTrack();
    }

    public function getDamageDealt(): ?int
    {
        return $this->damage_dealt;
    }

    public function setDamageDealt(?int $damage_dealt): self
    {
        $this->damage_dealt = $damage_dealt;

        return $this;
    }

    public function getDamageAssistedTrack(): ?int
    {
        return $this->damage_assisted_track;
    }

    public function setDamageAssistedTrack(?int $damage_assisted_track): self
    {
        $this->damage_assisted_track = $damage_assisted_track;

        return $this;
    }

    public function getExplosionHits(): ?int
    {
        return $this->explosion_hits;
    }

    public function setExplosionHits(?int $explosion_hits): self
    {
        $this->explosion_hits = $explosion_hits;

        return $this;
    }

    public function getDamageReceived(): ?int
    {
        return $this->damage_received;
    }

    public function setDamageReceived(?int $damage_received): self
    {
        $this->damage_received = $damage_received;

        return $this;
    }

    public function getPiercings(): ?int
    {
        return $this->piercings;
    }

    public function setPiercings(?int $piercings): self
    {
        $this->piercings = $piercings;

        return $this;
    }

    public function getShots(): ?int
    {
        return $this->shots;
    }

    public function setShots(?int $shots): self
    {
        $this->shots = $shots;

        return $this;
    }

    public function getExplosionHitsReceived(): ?int
    {
        return $this->explosion_hits_received;
    }

    public function setExplosionHitsReceived(?int $explosion_hits_received): self
    {
        $this->explosion_hits_received = $explosion_hits_received;

        return $this;
    }

    public function getDamageAssistedRadio(): ?int
    {
        return $this->damage_assisted_radio;
    }

    public function setDamageAssistedRadio(?int $damage_assisted_radio): self
    {
        $this->damage_assisted_radio = $damage_assisted_radio;

        return $this;
    }

    public function getXp(): ?int
    {
        return $this->xp;
    }

    public function setXp(?int $xp): self
    {
        $this->xp = $xp;

        return $this;
    }

    public function getDirectHitsReceived(): ?int
    {
        return $this->direct_hits_received;
    }

    public function setDirectHitsReceived(?int $direct_hits_received): self
    {
        $this->direct_hits_received = $direct_hits_received;

        return $this;
    }

    public function getFrags(): ?int
    {
        return $this->frags;
    }

    public function setFrags(?int $frags): self
    {
        $this->frags = $frags;

        return $this;
    }

    public function getSurvivedBattles(): ?int
    {
        return $this->survived_battles;
    }

    public function setSurvivedBattles(?int $survived_battles): self
    {
        $this->survived_battles = $survived_battles;

        return $this;
    }

    public function getDroppedCapturePoints(): ?int
    {
        return $this->dropped_capture_points;
    }

    public function setDroppedCapturePoints(?int $dropped_capture_points): self
    {
        $this->dropped_capture_points = $dropped_capture_points;

        return $this;
    }

    public function getMaxXp(): ?int
    {
        return $this->max_xp;
    }

    public function setMaxXp(?int $max_xp): self
    {
        $this->max_xp = $max_xp;

        return $this;
    }

    public function getTreesCut(): ?int
    {
        return $this->trees_cut;
    }

    public function setTreesCut(?int $trees_cut): self
    {
        $this->trees_cut = $trees_cut;

        return $this;
    }

    public function getMaxFrags(): ?int
    {
        return $this->max_frags;
    }

    public function setMaxFrags(?int $max_frags): self
    {
        $this->max_frags = $max_frags;

        return $this;
    }

    public function getMarkOfMastery(): ?int
    {
        return $this->mark_of_mastery;
    }

    public function setMarkOfMastery(?int $mark_of_mastery): self
    {
        $this->mark_of_mastery = $mark_of_mastery;

        return $this;
    }

    public function getBattleLifeTime(): ?int
    {
        return $this->battle_life_time;
    }

    public function setBattleLifeTime(?int $battle_life_time): self
    {
        $this->battle_life_time = $battle_life_time;

        return $this;
    }

    public function getPlayer(): ?Player
    {
        return $this->player;
    }

    public function setPlayer(?Player $player): self
    {
        $this->player = $player;

        return $this;
    }

    public function canUpdateStats($last_battle_time_unix): bool
    {
        return (string)$this->getLastBattleTime() !== (string)$last_battle_time_unix;
    }

    public function getLastBattleTime(): ?string
    {
        return $this->last_battle_time;
    }

    public function setLastBattleTime(string $last_battle_time): self
    {
        $this->last_battle_time = $last_battle_time;

        return $this;
    }
}
