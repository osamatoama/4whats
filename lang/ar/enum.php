<?php

use App\Enums\CampaignType;
use App\Enums\MessageTemplate;
use App\Enums\ProviderType;
use App\Enums\SettingKey;
use App\Enums\SubscriptionType;
use App\Enums\UserRole;
use App\Enums\Whatsapp\MessageStatus;
use App\Enums\Whatsapp\MessageType;

return [
    UserRole::class => [
        UserRole::ADMIN->name => 'مسؤول',
        UserRole::MERCHANT->name => 'تاجر',
        UserRole::EMPLOYEE->name => 'موظف',
    ],
    ProviderType::class => [
        ProviderType::SALLA->name => 'سلة',
        ProviderType::ZID->name => 'زد',
    ],
    MessageTemplate::class => [
        MessageTemplate::ORDER_STATUSES->name => [
            'default' => 'من فضلك قم بتغيير نص الرسالة قبل التفعيل',
            'label' => 'رسائل حالات الطلب',
            'description' => 'نص الرسالة الذي سوف يرسل للعميل، يمكنك استخدام الاختصارات التالية:',
            'hint' => 'رسائل حالات الطلب',
        ],

        MessageTemplate::SALLA_ABANDONED_CART->name => [
            'default' => 'من فضلك قم بتغيير نص الرسالة قبل التفعيل',
            'label' => 'رسائل السلات المتروكة',
            'description' => 'من اجل ادراج معلومات العميل في رسالة السلة المتروكة يمكنك استخدام الإختصارات التالية ضمن النص:',
            'hint' => 'رسائل السلات المتروكة',
        ],
        MessageTemplate::SALLA_OTP->name => [
            'default' => 'من فضلك قم بتغيير نص الرسالة قبل التفعيل',
            'label' => 'رسالة تحقق OTP',
            'description' => 'من اجل ادراج رمز التحقق في الرسالة استخدم الإختصار التالي:',
        ],
        MessageTemplate::SALLA_CUSTOMER_CREATED->name => [
            'default' => 'من فضلك قم بتغيير نص الرسالة قبل التفعيل',
            'label' => 'رسالة الترحيب بالعميل',
            'description' => 'من اجل ادراج اسم العميل في الرسالة استخدم الإختصار التالي:',
            'hint' => 'رسالة الترحيب بالعميل',
        ],
        MessageTemplate::SALLA_REVIEW_ORDER->name => [
            'default' => 'من فضلك قم بتغيير نص الرسالة قبل التفعيل',
            'label' => 'رسالة تقييم الطلب',
            'description' => 'من اجل ادراج معلومات العميل في الرسالة يمكنك استخدام الإختصارات التالية ضمن النص:',
            'hint' => 'رسالة تقييم الطلب',
        ],
        MessageTemplate::SALLA_COD->name => [
            'default' => 'من فضلك قم بتغيير نص الرسالة قبل التفعيل',
            'label' => 'رسالة الدفع عند الاستلام',
            'description' => 'من اجل ادراج متغيرات في الرسالة استخدم الإختصارات التالية:',
        ],
        MessageTemplate::SALLA_DIGITAL_PRODUCT->name => [
            'default' => 'من فضلك قم بتغيير نص الرسالة قبل التفعيل',
            'label' => 'رسالة تسليم المنتج الرقمي',
            'description' => 'من اجل ادراج متغيرات في الرسالة استخدم الإختصارات التالية:',
        ],
        MessageTemplate::SALLA_NEW_ORDER_FOR_EMPLOYEES->name => [
            'default' => 'من فضلك قم بتغيير نص الرسالة قبل التفعيل',
            'label' => 'رسالة الطلبات الجديدة للموظفين في المتجر',
            'description' => 'من اجل ادراج متغيرات في الرسالة استخدم الإختصارات التالية:',
            'hint' => 'رسالة الطلبات الجديدة للموظفين في المتجر',
        ],

        MessageTemplate::ZID_ABANDONED_CART->name => [
            'default' => 'من فضلك قم بتغيير نص الرسالة قبل التفعيل',
            'label' => 'رسائل السلات المتروكة',
            'description' => 'من اجل ادراج معلومات العميل في رسالة السلة المتروكة يمكنك استخدام الإختصارات التالية ضمن النص:',
            'hint' => 'رسائل السلات المتروكة',
        ],
        MessageTemplate::ZID_CUSTOMER_CREATED->name => [
            'default' => 'من فضلك قم بتغيير نص الرسالة قبل التفعيل',
            'label' => 'رسالة الترحيب بالعميل',
            'description' => 'من اجل ادراج اسم العميل في الرسالة استخدم الإختصار التالي:',
            'hint' => 'رسالة الترحيب بالعميل',
        ],
        MessageTemplate::ZID_COD->name => [
            'default' => 'من فضلك قم بتغيير نص الرسالة قبل التفعيل',
            'label' => 'رسالة الدفع عند الاستلام',
            'description' => 'من اجل ادراج متغيرات في الرسالة استخدم الإختصارات التالية:',
        ],
        MessageTemplate::ZID_DIGITAL_PRODUCT->name => [
            'default' => 'من فضلك قم بتغيير نص الرسالة قبل التفعيل',
            'label' => 'رسالة تسليم المنتج الرقمي',
            'description' => 'من اجل ادراج متغيرات في الرسالة استخدم الإختصارات التالية:',
        ],
        MessageTemplate::ZID_NEW_ORDER_FOR_EMPLOYEES->name => [
            'default' => 'من فضلك قم بتغيير نص الرسالة قبل التفعيل',
            'label' => 'رسالة الطلبات الجديدة للموظفين في المتجر',
            'description' => 'من اجل ادراج متغيرات في الرسالة استخدم الإختصارات التالية:',
            'hint' => 'رسالة الطلبات الجديدة للموظفين في المتجر',
        ],
    ],
    MessageStatus::class => [
        MessageStatus::PENDING->name => 'بانتظار المراجعه',
        MessageStatus::SENT->name => 'تم الارسال',
        MessageStatus::DELIVERED->name => 'تم الاستلام',
        MessageStatus::VIEWED->name => 'تم المشاهدة',
        MessageStatus::PLAYED->name => 'تم التشغيل',
    ],
    SettingKey::class => [
        SettingKey::SYSTEM_FOUR_WHATS_VOUCHER->name => 'Voucher',
    ],
    CampaignType::class => [
        CampaignType::CONTACTS->name => 'أرقام العملاء',
        CampaignType::ABANDONED_CARTS->name => 'السلات المتروكة',
    ],
    MessageType::class => [
        MessageType::TEXT->name => 'نصية',
        MessageType::FILE->name => 'نص مع ملف',
        MessageType::IMAGE->name => 'نص مع صورة',
        MessageType::VIDEO->name => 'نص مع فيديو',
        MessageType::AUDIO->name => 'صوت',
    ],
    SubscriptionType::class => [
        SubscriptionType::NONE->name => 'لا يوجد',
        SubscriptionType::TRIAL->name => 'فترة تجريبية',
        SubscriptionType::PAID->name => 'مدفوع',
    ],
];
