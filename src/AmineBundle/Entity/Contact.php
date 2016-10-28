<?php
// src/Blogger/BlogBundle/Entity/Enquiry.php
namespace AmineBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class Contact
{
  /**
   * @Assert\NotBlank()
   */
  protected $name;

  /**
   * @Assert\Email(
   *	message = "The email '{{ value }}' is not a valid email.",
   *    checkMX = true
   * )
   */
  protected $email;

  /**
   * @Assert\Length(min=10, minMessage="Le texte doit faire au moins {{ limit }} caractères.")
   * @Assert\NotBlank()
   */
  protected $subject;

  /**
   * @Assert\Length(min=4)
   */
  protected $body;

  public function getName()
  {
    return $this->name;
  }

  public function setName($name)
  {
    $this->name = $name;
  }

  public function getEmail()
  {
    return $this->email;
  }

  public function setEmail($email)
  {
    $this->email = $email;
  }

  public function getSubject()
  {
    return $this->subject;
  }

  public function setSubject($subject)
  {
    $this->subject = $subject;
  }

  public function getBody()
  {
    return $this->body;
  }

  public function setBody($body)
  {
    $this->body = $body;
  }
}
