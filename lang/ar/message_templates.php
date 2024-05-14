<?php

use App\Enums\MessageTemplates\SallaMessageTemplate;

return [
    SallaMessageTemplate::ABANDONED_CART->value => [
        'label' => 'رسائل السلات المتروكة',
        'description' => 'من اجل ادراج معلومات العميل في رسالة السلة المتروكة يمكنك استخدام الإختصارات التالية ضمن النص: :placeholders',
    ],
    SallaMessageTemplate::OTP->value => [
        'label' => 'رسالة تحقق OTP',
        'description' => 'من اجل ادراج رمز التحقق في الرسالة استخدم الإختصار التالي: :placeholders',
    ],
    SallaMessageTemplate::CUSTOMER_CREATED->value => [
        'label' => 'رسالة الترحيب بالعميل',
        'description' => 'من اجل ادراج اسم العميل في الرسالة استخدم الإختصار التالي: :placeholders',
    ],
    SallaMessageTemplate::REVIEW_ORDER->value => [
        'label' => 'رسالة تقييم الطلب',
        'description' => 'من اجل ادراج معلومات العميل في الرسالة يمكنك استخدام الإختصارات التالية ضمن النص: :placeholders',
    ],
    SallaMessageTemplate::COD->value => [
        'label' => 'رسالة الدفع عند الاستلام',
        'description' => 'من اجل ادراج متغيرات في الرسالة استخدم الإختصارات التالية: :placeholders',
    ],
    SallaMessageTemplate::NEW_ORDER_FOR_EMPLOYEES->value => [
        'label' => 'رسالة الطلبات الجديدة للموظفين في المتجر',
        'description' => 'من اجل ادراج متغيرات في الرسالة استخدم الإختصارات التالية: :placeholders',
    ],
    SallaMessageTemplate::ORDER_STATUSES->value => [
        'label' => 'رسائل حالات الطلب',
        'description' => 'نص الرسالة الذي سوف يرسل للعميل، يمكنك استخدام الاختصارات التالية: :placeholders',
    ],
];
