<?php /** @noinspection PhpMissingFieldTypeInspection */

namespace App\Domain\Auth;

use DateTime;
use DateTimeZone;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Symfony\Component\Security\Core\User\{
    PasswordAuthenticatedUserInterface,
    UserInterface
};
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author Quentin Boitel <quentin.boitel@outlook.fr>
 */
#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\Column(type: "uuid_binary", unique: true)]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    protected $id;

    #[ORM\Column(type: Types::STRING, length: 180, unique: true)]
    private string $username;

    #[ORM\Column(type: Types::JSON)]
    private array $roles;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private string $password;

    #[ORM\Column(type: Types::STRING, length: 255, unique: true)]
    #[Assert\NotBlank]
    #[Assert\Email]
    private ?string $email;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private DateTime $createdAt;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private DateTime $updatedAt;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $isBanned = false;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $isVerified = false;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $googleID;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $githubID;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        $this->createdAt = new DateTime(
            "now",
            new DateTimeZone("Europe/Paris")
        );
        $this->updatedAt = new DateTime(
            "now",
            new DateTimeZone("Europe/Paris")
        );
        $this->roles = ["ROLE_USER"];
    }

    public function getId()
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;
        return $this;
    }

    /**
     * A visual identifier that represents this user.
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return $this->username;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = "ROLE_USER";
        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function isBanned(): bool
    {
        return $this->isBanned;
    }

    public function setIsBanned(bool $isBanned): self
    {
        $this->isBanned = $isBanned;
        return $this;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;
        return $this;
    }

    public function getGoogleID(): ?string
    {
        return $this->googleID;
    }

    public function setGoogleID(?string $googleID): self
    {
        $this->googleID = $googleID;
        return $this;
    }

    public function getGithubID(): ?string
    {
        return $this->githubID;
    }

    public function setGithubID(?string $githubID): self
    {
        $this->githubID = $githubID;
        return $this;
    }
}
