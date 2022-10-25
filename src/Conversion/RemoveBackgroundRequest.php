<?php declare(strict_types=1);

namespace Uploadcare\Conversion;

use Uploadcare\Interfaces\Conversion\RemoveBackgroundRequestInterface;

class RemoveBackgroundRequest implements RemoveBackgroundRequestInterface
{
    private bool $crop = false;
    private string $cropMargin = '0';
    private ?string $scale = null;
    private bool $addShadow = false;
    private string $typeLevel = 'none';
    private string $type = 'auto';
    private bool $semitransparency = true;
    private string $channels = 'rgba';
    private ?string $roi = null;
    private ?string $position = null;

    public static function rules(): array
    {
        return [
            'crop' => 'bool',
            'cropMargin' => 'string',
            'scale' => 'string',
            'addShadow' => 'bool',
            'typeLevel' => 'string',
            'type' => 'string',
            'semitransparency' => 'bool',
            'channels' => 'string',
            'roi' => 'string',
            'position' => 'string',
        ];
    }

    public function getCrop(): bool
    {
        return $this->crop;
    }

    public function setCrop(bool $crop): self
    {
        $this->crop = $crop;

        return $this;
    }

    public function getCropMargin(): string
    {
        return $this->cropMargin;
    }

    public function setCropMargin(string $cropMargin): self
    {
        $this->cropMargin = $cropMargin;

        return $this;
    }

    public function getScale(): ?string
    {
        return $this->scale;
    }

    public function setScale(?string $scale): self
    {
        $this->scale = $scale;

        return $this;
    }

    public function getAddShadow(): bool
    {
        return $this->addShadow;
    }

    public function setAddShadow(bool $addShadow): self
    {
        $this->addShadow = $addShadow;

        return $this;
    }

    public function getTypeLevel(): string
    {
        return $this->typeLevel;
    }

    public function setTypeLevel(string $typeLevel): self
    {
        $this->typeLevel = $typeLevel;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getSemitransparency(): bool
    {
        return $this->semitransparency;
    }

    public function setSemitransparency(bool $semitransparency): self
    {
        $this->semitransparency = $semitransparency;

        return $this;
    }

    public function getChannels(): string
    {
        return $this->channels;
    }

    public function setChannels(string $channels): self
    {
        $this->channels = $channels;

        return $this;
    }

    public function getRoi(): ?string
    {
        return $this->roi;
    }

    public function setRoi(?string $roi): self
    {
        $this->roi = $roi;

        return $this;
    }

    public function getPosition(): ?string
    {
        return $this->position;
    }

    public function setPosition(?string $position): self
    {
        $this->position = $position;

        return $this;
    }
}
