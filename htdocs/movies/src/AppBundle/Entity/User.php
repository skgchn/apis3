<?php

namespace AppBundle\Entity;

use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 * @ORM\Table(name="user")
 * @UniqueEntity("username")
 */
class User implements UserInterface {
    
    const ROLE_USER = 'ROLE_USER';
    const ROLE_ADMIN = 'ROLE_ADMIN';
    
    /**
     * @var int 
     * 
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(name="id", type="integer")
     * @Serializer\Groups({"Default", "Deserialize", "Patch"})
     */
    private $id;
    
    /**
     * @var string
     * 
     * @ORM\Column(name="username", type="string", length=100, unique=true)
     * @Assert\NotBlank(groups={"Default"})
     * @Serializer\Groups({"Default", "Deserialize"})
     */
    private $username;
    
    /**
     * @var string
     * @ORM\Column(name="password", type="string")
     * @Assert\NotBlank(groups={"Default"})
     * @Assert\Expression(
     *     "this.whenPresentIsNotEmpty()",
     *      message="Password must be 7 or more characters long and contain at least one digit, one upper case and one lower case letter",
     *      groups={"Patch"}
     * )
     * Below Regex uses a look around (look ahead) PHP regular expression syntax
     * @Assert\Regex(
     *     pattern="/(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9]).{7,}/",
     *     message="Password must be 7 or more characters long and contain at least one digit, one upper case and one lower case letter",
     *     groups={"Default", "Patch"}
     * )
     * @Serializer\Groups({"Deserialize", "Patch"})
     */
    private $password;

    
    /**
     * @var string
     * @Assert\NotBlank(groups={"Default"})
     * @Assert\Expression(
     *     "this.getPassword() === this.getRetypedPassword()",
     *      message="Passwords do not match",
     *      groups={"Default", "Patch"}
     * )
     * Not saved in DB, so serializer needs to know type.
     * @Serializer\Type("string")
     * @Serializer\Groups({"Deserialize", "Patch"});
     */
    private $retypedPassword;
    
    /**
     * @var array
     * @ORM\Column(type="simple_array", length=200)
     * @Serializer\Exclude()
     */
    private $roles;
    
    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */    
    public function eraseCredentials() {
        
    }

    /**
     * Returns the password used to authenticate the user.
     *
     * This should be the encoded password. On authentication, a plain-text
     * password will be salted, encoded, and then compared to this value.
     *
     * @return string The password
     */
    public function getPassword(): ?string {
        return $this->password;
    }

    /**
     * Returns the roles granted to the user.
     *
     * <code>
     * public function getRoles()
     * {
     *     return array('ROLE_USER');
     * }
     * </code>
     *
     * Alternatively, the roles might be stored on a ``roles`` property,
     * and populated in any number of different ways when the user object
     * is created.
     *
     * @return (Role|string)[] The user roles
     */
    public function getRoles() {
        return $this->roles;
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt() {
        
    }

    /**
     * Returns the username used to authenticate the user.
     *
     * @return string The username
     */
    public function getUsername(): string {
        return $this->username;
    }

    /**
     * 
     * @param string $username
     */
    function setUsername(string $username) {
        $this->username = $username;
    }
    
    /**
     * @param string $password
     */
    function setPassword($password) {
        $this->password = $password;
    }
    
    /**
     * @return int
     */
    function getId(): int {
        return $this->id;
    }

    /*
     * @return string
     */
    function getRetypedPassword(): ?string {
        return $this->retypedPassword;
    }

    /**
     * @param string $retypedPassword
     */
    function setRetypedPassword($retypedPassword) {
        $this->retypedPassword = $retypedPassword;
    }

    /**
     * @param array $roles
     */
    function setRoles($roles) {
        $this->roles = $roles;
    }
    
    function whenPresentIsNotEmpty() {
        if ((!is_null($this->getPassword())) && empty($this->getPassword())) {
            return false;
        }
        return true;
    }

}
