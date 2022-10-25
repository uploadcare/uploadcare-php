<?php

namespace Uploadcare\Interfaces\Conversion;

use Uploadcare\Interfaces\SerializableInterface;

interface RemoveBackgroundRequestInterface extends SerializableInterface
{
    /**
     * Whether to crop off all empty regions.
     */
    public function getCrop(): bool;

    /**
     * Adds a margin around the cropped subject, e.g. 30px or 30%.
     * Default: "0".
     */
    public function getCropMargin(): string;

    /**
     * Scales the subject relative to the total image size, e.g. 80%.
     */
    public function getScale(): ?string;

    /**
     * Whether to add an artificial shadow to the result.
     * Default: `false`.
     */
    public function getAddShadow(): bool;

    /**
     * Enum: "none" "1" "2" "latest".
     * Default: "none"
     *      - "none" = No classification (foreground_type won't bet set in the application data)
     *      - "1" = Use coarse classification classes: [person, product, animal, car, other]
     *      - "2" = Use more specific classification classes: [person, product, animal, car, car_interior, car_part, transportation, graphics, other]
     *      - "latest" = Always use the latest classification classes available.
     */
    public function getTypeLevel(): string;

    /**
     * Foreground type.
     * Enum: "auto" "person" "product" "car".
     */
    public function getType(): string;

    /**
     * Whether to have semi-transparent regions in the result.
     * Default: `true`.
     */
    public function getSemitransparency(): bool;

    /**
     * Request either the finalized image ('rgba', default) or an alpha mask ('alpha').
     * Enum: "rgba" "alpha"
     * Default: "rgba".
     */
    public function getChannels(): string;

    /**
     * Region of interest: Only contents of this rectangular region can be detected as foreground. Everything outside is
     * considered background and will be removed. The rectangle is defined as two x/y coordinates in the
     * format "x1 y1 x2 y2". The coordinates can be in absolute pixels (suffix 'px') or relative to the width/height
     * of the image (suffix '%'). By default, the whole image is the region of interest ("0% 0% 100% 100%").
     */
    public function getRoi(): ?string;

    /**
     * Positions the subject within the image canvas. Can be "original" (default unless "scale" is given), "center"
     * (default when "scale" is given) or a value from "0%" to "100%" (both horizontal and vertical)
     * or two values (horizontal, vertical).
     */
    public function getPosition(): ?string;
}
