<?php

use App\Enums\MessageTemplates\SallaMessageTemplate;

return [
    SallaMessageTemplate::ABANDONED_CART->name => [
        'default' => 'من فضلك قم بتغيير نص الرسالة قبل التفعيل',
        'label' => 'رسائل السلات المتروكة',
        'description' => 'من اجل ادراج معلومات العميل في رسالة السلة المتروكة يمكنك استخدام الإختصارات التالية ضمن النص: :placeholders',
        'hint' => '',
    ],
    SallaMessageTemplate::OTP->name => [
        'default' => 'من فضلك قم بتغيير نص الرسالة قبل التفعيل',
        'label' => 'رسالة تحقق OTP',
        'description' => 'من اجل ادراج رمز التحقق في الرسالة استخدم الإختصار التالي: :placeholders',
    ],
    SallaMessageTemplate::CUSTOMER_CREATED->name => [
        'default' => 'من فضلك قم بتغيير نص الرسالة قبل التفعيل',
        'label' => 'رسالة الترحيب بالعميل',
        'description' => 'من اجل ادراج اسم العميل في الرسالة استخدم الإختصار التالي: :placeholders',
        'hint' => '',
    ],
    SallaMessageTemplate::REVIEW_ORDER->name => [
        'default' => 'من فضلك قم بتغيير نص الرسالة قبل التفعيل',
        'label' => 'رسالة تقييم الطلب',
        'description' => 'من اجل ادراج معلومات العميل في الرسالة يمكنك استخدام الإختصارات التالية ضمن النص: :placeholders',
        'hint' => '',
    ],
    SallaMessageTemplate::COD->name => [
        'default' => 'من فضلك قم بتغيير نص الرسالة قبل التفعيل',
        'label' => 'رسالة الدفع عند الاستلام',
        'description' => 'من اجل ادراج متغيرات في الرسالة استخدم الإختصارات التالية: :placeholders',
    ],
    SallaMessageTemplate::NEW_ORDER_FOR_EMPLOYEES->name => [
        'default' => 'من فضلك قم بتغيير نص الرسالة قبل التفعيل',
        'label' => 'رسالة الطلبات الجديدة للموظفين في المتجر',
        'description' => 'من اجل ادراج متغيرات في الرسالة استخدم الإختصارات التالية: :placeholders',
        'hint' => '',
    ],
    SallaMessageTemplate::ORDER_STATUSES->name => [
        'default' => 'من فضلك قم بتغيير نص الرسالة قبل التفعيل',
        'label' => 'رسائل حالات الطلب',
        'description' => 'نص الرسالة الذي سوف يرسل للعميل، يمكنك استخدام الاختصارات التالية: :placeholders',
        'hint' => '',
    ],
];
