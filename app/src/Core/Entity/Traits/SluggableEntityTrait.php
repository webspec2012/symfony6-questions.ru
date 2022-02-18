<?php
namespace App\Core\Entity\Traits;

use App\Core\Exception\EntityValidationException;
use Doctrine\ORM\Mapping as ORM;

/**
 * Обеспечивает работу с полями slug, href у Entity
 */
trait SluggableEntityTrait
{
    /**
     * @var string Slug
     *
     * @ORM\Column(
     *     type="string",
     *     length=200,
     *     nullable=false,
     * )
     */
    protected string $slug;

    /**
     * @ORM\Column(
     *     type="string",
     *     length=250,
     *     nullable=false
     * )
     */
    protected string $href = '';

    /**
     * @return string Slug
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * Установить Slug
     *
     * @param string $slug Slug
     * @return static
     * @throws EntityValidationException
     */
    public function setSlug(string $slug): static
    {
        if (empty($slug) || !preg_match('/^[-_\w]+$/isU', $slug)) {
            throw new EntityValidationException(sprintf("Передан невалидный slug '%s'", $slug));
        }

        $this->slug = mb_strtolower(mb_substr($slug, 0, 200));

        return $this;
    }

    /**
     * @return string Href
     */
    public function getHref(): string
    {
        return $this->href;
    }

    /**
     * Установить Ссылку
     *
     * @param string $href Ссылка
     * @return static
     */
    public function setHref(string $href): static
    {
        $this->href = $href;

        return $this;
    }
}
