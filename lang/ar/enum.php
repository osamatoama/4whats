<?php

use App\Enums\ProviderType;
use App\Enums\StoreMessageTemplate;
use App\Enums\UserRole;
use App\Enums\Whatsapp\MessageStatus;

return [
    UserRole::class => [
        UserRole::ADMIN->name => 'مسؤول',
        UserRole::MERCHANT->name => 'تاجر',
        UserRole::EMPLOYEE->name => 'موظف',
    ],
    ProviderType::class => [
        ProviderType::SALLA->name => 'سلة',
    ],
    StoreMessageTemplate::class => [
        StoreMessageTemplate::ORDER_STATUSES->name => [
            'default' => 'من فضلك قم بتغيير نص الرسالة قبل التفعيل',
            'label' => 'رسائل حالات الطلب',
            'description' => 'نص الرسالة الذي سوف يرسل للعميل، يمكنك استخدام الاختصارات التالية: :placeholders',
            'hint' => 'رسائل حالات الطلب',
        ],

        StoreMessageTemplate::SALLA_ABANDONED_CART->name => [
            'default' => 'من فضلك قم بتغيير نص الرسالة قبل التفعيل',
            'label' => 'رسائل السلات المتروكة',
            'description' => 'من اجل ادراج معلومات العميل في رسالة السلة المتروكة يمكنك استخدام الإختصارات التالية ضمن النص: :placeholders',
            'hint' => 'رسائل السلات المتروكة',
        ],
        StoreMessageTemplate::SALLA_OTP->name => [
            'default' => 'من فضلك قم بتغيير نص الرسالة قبل التفعيل',
            'label' => 'رسالة تحقق OTP',
            'description' => 'من اجل ادراج رمز التحقق في الرسالة استخدم الإختصار التالي: :placeholders',
        ],
        StoreMessageTemplate::SALLA_CUSTOMER_CREATED->name => [
            'default' => 'من فضلك قم بتغيير نص الرسالة قبل التفعيل',
            'label' => 'رسالة الترحيب بالعميل',
            'description' => 'من اجل ادراج اسم العميل في الرسالة استخدم الإختصار التالي: :placeholders',
            'hint' => 'رسالة الترحيب بالعميل',
        ],
        StoreMessageTemplate::SALLA_REVIEW_ORDER->name => [
            'default' => 'من فضلك قم بتغيير نص الرسالة قبل التفعيل',
            'label' => 'رسالة تقييم الطلب',
            'description' => 'من اجل ادراج معلومات العميل في الرسالة يمكنك استخدام الإختصارات التالية ضمن النص: :placeholders',
            'hint' => 'رسالة تقييم الطلب',
        ],
        StoreMessageTemplate::SALLA_COD->name => [
            'default' => 'من فضلك قم بتغيير نص الرسالة قبل التفعيل',
            'label' => 'رسالة الدفع عند الاستلام',
            'description' => 'من اجل ادراج متغيرات في الرسالة استخدم الإختصارات التالية: :placeholders',
        ],
        StoreMessageTemplate::SALLA_NEW_ORDER_FOR_EMPLOYEES->name => [
            'default' => 'من فضلك قم بتغيير نص الرسالة قبل التفعيل',
            'label' => 'رسالة الطلبات الجديدة للموظفين في المتجر',
            'description' => 'من اجل ادراج متغيرات في الرسالة استخدم الإختصارات التالية: :placeholders',
            'hint' => 'رسالة الطلبات الجديدة للموظفين في المتجر',
        ],
    ],
    MessageStatus::class => [
        MessageStatus::PENDING->name => 'بانتظار المراجعه',
        MessageStatus::SENT->name => 'تم الارسال',
        MessageStatus::DELIVERED->name => 'تم الاستلام',
        MessageStatus::VIEWED->name => 'تم المشاهدة',
    ],
];
