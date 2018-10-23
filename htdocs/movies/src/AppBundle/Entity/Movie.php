<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Hateoas\Configuration\Annotation as Hateoas;
use JMS\Serializer\Annotation as Serializer;

/**
 * Movie
 *
 * @ORM\Table(name="movie")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MovieRepository")
 * NOTE: full class path has to be given here. Cannot use MovieRepository here with use AppBundle\Repository\MovieRepository above.
 * @Hateoas\Relation("roles", href=@Hateoas\Route("get_movie_roles", parameters={"movie" = "expr(object.getId())"}))
 */
class Movie
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Length(max=255)
     */
    private $title;

    /**
     * @var int
     *
     * @ORM\Column(name="year", type="smallint")
     * @Assert\NotBlank()
     * @Assert\Range(min=1888, max=2020)
     * 
     */
    private $year;

    /**
     * @var int
     *
     * @ORM\Column(name="time", type="smallint")
     * @Assert\NotBlank()
     * @Assert\Range(min=1, max=300)
     */
    private $time;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     * @Assert\NotBlank()
     */
    private $description;
    
    /**
     * @var ArrayCollection
     * 
     * @ORM\OneToMany(targetEntity="MovieRole", mappedBy="movie")
     * @Serializer\Exclude()
     */
    private $roles;
    
    public function __construct() {
        $this->roles = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Movie
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set year
     *
     * @param integer $year
     *
     * @return Movie
     */
    public function setYear($year)
    {
        $this->year = $year;

        return $this;
    }

    /**
     * Get year
     *
     * @return int
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * Set time
     *
     * @param integer $time
     *
     * @return Movie
     */
    public function setTime($time)
    {
        $this->time = $time;

        return $this;
    }

    /**
     * Get time
     *
     * @return int
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Movie
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }
    
    /**
     * 
     * @return Collection
     */
    function getRoles(): Collection {
        return $this->roles;
    }


}
