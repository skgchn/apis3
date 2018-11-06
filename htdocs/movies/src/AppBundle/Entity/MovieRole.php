<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as App;
use JMS\Serializer\Annotation as Serializer;
use Hateoas\Configuration\Annotation as Hateoas;

/**
 * MovieRole
 *
 * @ORM\Table(name="movie_role", indexes={@ORM\Index(name="movie_role_idx", columns={"movie_id", "role"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MovieRoleRepository")
 * By default no properties to be serialized.
 * @Serializer\ExclusionPolicy("ALL")
 * @Hateoas\Relation(
 *     "person",
 *     href=@Hateoas\Route("get_person", parameters={"person" = "expr(object.getPerson().getId())"})
 * )
 */
class MovieRole {
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Serializer\Groups({"Default", "Deserialize"})
     * @Serializer\Expose()
     */
    private $id;

    /*
     * Many roles are in a movie. This table (Many), other table (One).
     * Since the many side in a One-to-Many association holds the foreign key,
     * it makes it the owning side.
     * Owning side specifies inversedBy. The other side specifies mappedBy.
     * inversedBy is specifying the property in the other side Class.
     * Note, this property will only be in the other side's PHP object, and not in it's DB table. 
     */
    /**
     * @var Movie
     * 
     * @ORM\ManyToOne(targetEntity="Movie", inversedBy="roles")
     */
     //* default: @JoinColumn(name="movie_id", referencedColumnName="id")
     //* default: @ORM\Column(name="movie_id", type="integer")
    private $movie;
    
    /**
     * @var string
     * 
     * @ORM\Column(name="role", type="string", length=100)
     * @Assert\NotBlank()
     * @Assert\Length(min=1, max=100)
     * @Serializer\Groups({"Default", "Deserialize"})
     * @Serializer\Expose()
     */
    private $role;

    /* Many roles can be played by a single actor (person).*/
    
    /**
     * @var Person
     * 
     * @ORM\ManyToOne(targetEntity="Person")
     * @App\DeserializeEntity(className="AppBundle\Entity\Person", idFieldName="id", idGetterMethodName="getId", setterMethodName="setPerson")
     * @Assert\NotBlank()
     * @Serializer\Groups({"Deserialize"})
     * @Serializer\Expose()
     */
     // * default: @ORM\Column(name="person_id", type="integer")
    private $person;

    /* Getters and Setters */
    /**
     * 
     * @return int
     */
    function getId() {
        return $this->id;
    }

    /**
     * 
     * @return Movie
     */
    function getMovie() : Movie {
        return $this->movie;
    }

    /**
     * 
     * @return string $role
     */
    function getRole() : string {
        return $this->role;
    }

    /**
     * 
     * @return Person
     */
    function getPerson() : Person {
        return $this->person;
    }

    /**
     * 
     * @param Movie $movie
     * @return $MovieRole
     */
    function setMovie(Movie $movie) : MovieRole {
        $this->movie = $movie;
        
        return $this;
    }

    /**
     * 
     * @param string $role
     * @return MovieRole
     */
    function setRole(string $role) : MovieRole {
        $this->role = $role;
        
        return $this;
    }

    /**
     * 
     * @param Person $person
     * @return MovieRole
     */
    function setPerson(Person $person) : MovieRole {
        $this->person = $person;
        
        return $this;
    }

}
