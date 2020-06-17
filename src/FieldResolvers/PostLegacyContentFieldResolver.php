<?php

declare(strict_types=1);

namespace PoP\Posts\FieldResolvers;

use PoP\CustomPosts\Facades\CustomPostTypeAPIFacade;
use PoP\Posts\TypeResolvers\PostTypeResolver;
use PoP\ComponentModel\Schema\SchemaDefinition;
use PoP\Translation\Facades\TranslationAPIFacade;
use PoP\ComponentModel\TypeResolvers\TypeResolverInterface;
use PoP\ComponentModel\FieldResolvers\AbstractDBDataFieldResolver;
use PoP\CustomPosts\Types\Status;

class PostLegacyContentFieldResolver extends AbstractDBDataFieldResolver
{
    public static function getClassesToAttachTo(): array
    {
        return [
            PostTypeResolver::class,
        ];
    }

    public static function getFieldNamesToResolve(): array
    {
        return [
            'isPublished',
        ];
    }

    public function getSchemaFieldType(TypeResolverInterface $typeResolver, string $fieldName): ?string
    {
        $types = [
            'isPublished' => SchemaDefinition::TYPE_BOOL,
        ];
        return $types[$fieldName] ?? parent::getSchemaFieldType($typeResolver, $fieldName);
    }

    public function isSchemaFieldResponseNonNullable(TypeResolverInterface $typeResolver, string $fieldName): bool
    {
        $nonNullableFieldNames = [
            'isPublished',
        ];
        if (in_array($fieldName, $nonNullableFieldNames)) {
            return true;
        }
        return parent::isSchemaFieldResponseNonNullable($typeResolver, $fieldName);
    }

    public function getSchemaFieldDescription(TypeResolverInterface $typeResolver, string $fieldName): ?string
    {
        $translationAPI = TranslationAPIFacade::getInstance();
        $descriptions = [
            'isPublished' => $translationAPI->__('Has the post been published?', 'content'),
        ];
        return $descriptions[$fieldName] ?? parent::getSchemaFieldDescription($typeResolver, $fieldName);
    }

    public function getSchemaFieldDeprecationDescription(TypeResolverInterface $typeResolver, string $fieldName, array $fieldArgs = []): ?string
    {
        $translationAPI = TranslationAPIFacade::getInstance();
        $placeholder_status = $translationAPI->__('Use \'isStatus(status:%s)\' instead of \'%s\'', 'content');
        $descriptions = [
            'isPublished' => sprintf(
                $placeholder_status,
                Status::PUBLISHED,
                $fieldName
            ),
        ];
        return $descriptions[$fieldName] ?? parent::getSchemaFieldDeprecationDescription($typeResolver, $fieldName, $fieldArgs);
    }

    public function resolveValue(TypeResolverInterface $typeResolver, $resultItem, string $fieldName, array $fieldArgs = [], ?array $variables = null, ?array $expressions = null, array $options = [])
    {
        $customPostTypeAPI = CustomPostTypeAPIFacade::getInstance();
        $post = $resultItem;
        switch ($fieldName) {
            case 'isPublished':
                return Status::PUBLISHED == $customPostTypeAPI->getStatus($post);
        }

        return parent::resolveValue($typeResolver, $resultItem, $fieldName, $fieldArgs, $variables, $expressions, $options);
    }
}
