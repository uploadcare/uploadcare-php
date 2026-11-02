<?php declare(strict_types=1);

namespace Uploadcare\Interfaces\Api;

use Uploadcare\File\Tags;
use Uploadcare\Interfaces\File\FileInfoInterface;
use Uploadcare\Response\TagsResponse;

/**
 * File tags.
 *
 * Tags are normalized by the API before storage: lowercased, whitespace-stripped, duplicates
 * removed. Up to 50 tags per file, up to 100 characters each, and only Latin letters, digits,
 * hyphen, underscore and dot are allowed.
 *
 * @see https://uploadcare.com/docs/file-tags/
 */
interface TagsApiInterface
{
    /**
     * Current tag list of a file.
     *
     * @param string|FileInfoInterface $id Uuid of File
     */
    public function getTags($id): Tags;

    /**
     * Replace the entire tag set of a file. Tags not in the new list are deleted,
     * an empty array clears all tags.
     *
     * @param string|FileInfoInterface $id   Uuid of File
     * @param array<array-key, string> $tags New tag list
     *
     * @throws \Uploadcare\Exception\TagsException on invalid tags
     */
    public function replaceTags($id, array $tags): TagsResponse;

    /**
     * Add and/or remove tags in a single atomic operation. Delete is applied first, then add,
     * so a tag can be removed and re-added in one call. Calling it without arguments is a
     * valid no-op that returns the current state.
     *
     * @param string|FileInfoInterface $id     Uuid of File
     * @param array<array-key, string> $add    Tags to add, already present ones are skipped
     * @param array<array-key, string> $delete Tags to remove, absent ones are ignored
     *
     * @throws \Uploadcare\Exception\TagsException on invalid tags
     */
    public function updateTags($id, array $add = [], array $delete = []): TagsResponse;
}
