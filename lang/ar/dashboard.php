<?php

return [
    'common' => [
        'title' => '٤ واتس',
        'dashboard' => 'لوحة التحكم',
        'are_you_sure' => 'هل انت متاكد؟',
        'create' => 'اضافة',
        'edit' => 'تعديل',
        'delete' => 'حذف',
        'search' => 'بحث',
        'export' => 'تصدير',
        'exporting' => 'جاري التصدير ...',
        'back' => 'عودة',
        'actions' => 'الاجراءات',
        'no_data' => 'لا يوجد بيانات',
        'loading' => 'جاري التحميل ...',
        'second' => 'ثانية',
        'store_expired_message' => 'المتجر الخاص بك منتهي الصلاحية برجاء تجديد الاشتراك',
        'yes' => 'نعم',
        'no' => 'لا',
        'is_enabled' => 'مفعل',
        'sync' => 'مزامنه',
        'send' => 'ارسال',
        'something_went_wrong' => 'حدث خطا ما. برجاء المحاولة مرة اخري لاحقا',
        'click_here' => 'اضغط هنا',
    ],
    'navbar' => [
        'profile' => 'الملف الشخصي',
        'settings' => 'الاعدادات',
        'logout' => 'تسجيل الخروج',
    ],
    'footer' => [
        'copyrights' => '&copy; :date 4 واتس',
        'made_with_love_by_valinteca' => 'صنع بكل :icon بواسطة فالنتيكا',
    ],
    'pagination' => [
        'previous' => '&laquo;',
        'next' => '&raquo;',
        'showing' => 'عرض',
        'to' => 'الي',
        'of' => 'من',
        'results' => 'نتائج',
    ],
    'whatsapp' => [
        'connected' => 'الهاتف متصل بنجاح',
        'disconnected' => 'الهاتف غير متصل',
        'disconnect' => 'فصل الهاتف',
        'cannot_disconnect' => 'لا يمكن فصل الهاتف',
        'disconnecting' => 'جاري فصل الهاتف',
        'will_refresh_after' => 'سيتم التحديث بعد',
        'disable_sending' => 'ايقاف الارسال',
        'enable_sending' => 'تشغيل الارسال',
        'sending_disabled' => 'تم ايقاف الارسال',
        'sending_enabled' => 'تم تشغيل الارسال',
    ],
    'messages' => [
        'installing_app' => 'جاري تثبيت التطبيق. سيتم ارسال رسالة الي البريد الالكتروني تحتوي علي بيانات تسجيل الدخول.',
    ],
    'pages' => [
        'auth' => [
            'login' => [
                'title' => 'تسجيل الدخول',
                'login_to_continue_to_4whats' => 'سجل دخول لتكمل الي ٤ واتس',
                'email' => 'البريد الالكتروني',
                'password' => 'كلمة السر',
                'remember_me' => 'تذكرني',
                'forgot_password' => 'نسيت كلمة السر؟',
                'login' => 'تسجيل دخول',
                'login_using_zid' => 'تسجيل دخول باستخدام زد',
            ],
            'forgot_password' => [
                'title' => 'نسيت كلمة السر',
                'enter_your_email_and_instructions_will_be_sent_to_you' => 'ادخل بريدك الالكتروني وسيتم ارسال التعليمات لك',
                'email' => 'البريد الالكتروني',
                'send' => 'ارسال',
                'remember_it' => 'هل تتذكره؟',
                'login_here' => 'سجل الدخول من هنا',
            ],
            'reset_password' => [
                'title' => 'اعادة تعيين كلمة السر',
                'email' => 'البريد الالكتروني',
                'password' => 'كلمة المرور الجديدة',
                'password_confirmation' => 'تاكيد كلمة المرور الجديدة',
                'reset' => 'اعادة تعيين',
            ],
        ],
        'home' => [
            'title' => 'الصفحة الرئيسية',
            'contacts' => 'الارقام',
            'messages' => 'الرسائل المرسلة',
            'scan_qr_code' => 'امسح الكود عن طريق الواتساب لربط رقم الهاتف',
        ],
        'stores' => [
            'index' => [
                'title' => 'المتاجر',
                'page_title' => 'المتاجر',
            ],
            'columns' => [
                'id' => '#',
                'type' => 'النوع',
                'email' => 'البريد الالكتروني',
                'four_whats_provider_id' => 'ID',
                'four_whats_api_key' => 'API Key',
                'whatsapp_instance_id' => 'Instance ID',
                'whatsapp_instance_token' => 'Instance Token',
            ],
        ],
        'employees' => [
            'index' => [
                'title' => 'الموظفين',
                'page_title' => 'الموظفين',
                'employees_details' => 'تفاصيل الموظفين',
                'create_a_new_employee' => 'اضافة موظف جديد',
                'no_employees' => 'لا يوجد موظفين',
            ],
            'create' => [
                'title' => 'اضافة موظف جديد',
                'page_title' => 'اضافة موظف جديد',
                'employee_details' => 'تفاصيل الموظف',
            ],
            'columns' => [
                'id' => '#',
                'name' => 'الاسم',
                'email' => 'البريد الالكتروني',
            ],
        ],
        'templates' => [
            'index' => [
                'title' => 'إعدادات الرسائل التلقائية',
                'page_title' => 'الرسائل التلقائية (الويب هوك)',
                'waiting_before_sending' => 'مدة الإنتظار قبل ارسال الرسالة',
                'in_hours' => 'المدة تحتسب بالساعات',
                'order_status_to_send_notification' => 'حالة الطلب لإرسال رسالة إشعار طلب التقييم',
                'employees_mobiles' => 'الأرقام التي سوف تستلم رسالة الطلبات الجديدة',
                'employees_mobiles_description' => 'يمكنك ادخال اكثر من رقم عن طريق فصلهم بفاصلة, هذه الأرقام سوف تستلم رسالة عند كل طلب جديد في المتجر',
                'syncing_order_statuses' => 'جاري مزامنه حالات الطلب',
                'syncing_order_statuses_please_wait' => 'جاري مزامنه حالات الطلب. من فضلك انتظر',
                'salla_review_order_warning' => 'يجب تحديد حالة رسالة التقييم عبر إعدادات التقييم في متجر سلة',
            ],
            'columns' => [
                'message' => [
                    'label' => 'الرسالة',
                ],
                'delay_in_hours' => [
                    'label' => 'التاخير بالساعات',
                ],
                'mobiles' => [
                    'label' => 'ارقام الموظفين',
                ],
                'current_order_status_template' => [
                    'label' => 'قالب رسائل حالات الطلب الحالي',
                ],
                'review_order_status' => [
                    'label' => 'حالة الطلب الخاصة برسالة تقييم الطلب',
                ],
            ],
        ],
        'contacts' => [
            'index' => [
                'title' => 'أرقام العملاء',
                'page_title' => 'أرقام العملاء',
            ],
            'columns' => [
                'id' => '#',
                'name' => 'الاسم',
                'email' => 'البريد الالكتروني',
                'mobile' => 'رقم الهاتف',
                'created_at' => 'تاريخ الانشاء',
                'is_blacklisted' => 'في القائمة المحظورة',
            ],
            'actions' => [
                'add_to_blacklist' => 'اضف للقائمة المحظورة',
                'remove_from_blacklist' => 'حذف من القائمة المحظورة',
            ],
        ],
        'campaigns' => [
            'title' => 'رسائل الحملات',
            'sending_campaigns' => 'جاري ارسال (:count) حملات حاليا',
            'click_here_to_stop_sending' => 'اضغط هنا لايقاف الحملات',
            'send' => [
                'title' => 'ارسال الحملات',
                'page_title' => 'ارسال الحملات',
                'messages' => [
                    'sending' => 'جاري ارسال الحملة',
                ],
            ],
            'current' => [
                'title' => 'الحملات الجاري ارسالها',
                'page_title' => 'الحملات الجاري ارسالها',
                'actions' => [
                    'cancel' => 'الغاء الحملة',
                    'canceled' => 'تم الغاء الحملة',
                    'finished' => 'تم انتهاء الحملة',
                ],
                'messages' => [
                    'canceled' => 'تم الغاء الحملة بنجاح',
                ],
            ],
            'columns' => [
                'campaign_type' => [
                    'label' => 'نوع الحملة',
                ],
                'message_type' => [
                    'label' => 'نوع الرسالة',
                ],
                'message' => [
                    'label' => 'الرسالة',
                    'description' => 'نص الرسالة الذي سوف يرسل للعميل، يمكنك استخدام الاختصارات التالية:',
                ],
                'file' => [
                    'label' => 'الملف',
                ],
                'image' => [
                    'label' => 'الصورة',
                ],
                'video' => [
                    'label' => 'الفيديو',
                    'description' => 'يجب ان يكون من بصيغة mp4',
                ],
                'audio' => [
                    'label' => 'الصوت',
                    'description' => 'يجب ان يكون من بصيغة mp3',
                ],

                'created_at' => [
                    'label' => 'بداية الارسال',
                ],
                'percentage' => [
                    'label' => 'النسبة المئوية (%)',
                ],
            ],
        ],
        'messages' => [
            'index' => [
                'title' => 'سجل الارسال',
                'page_title' => 'سجل الارسال',
                'attachments' => 'المرفقات',
            ],
            'columns' => [
                'type' => 'النوع',
                'mobile' => 'رقم الهاتف',
                'message' => 'الرسالة المرسلة',
                'created_at' => 'تاريخ الارسال',
                'status' => 'حالة الارسال',
            ],
        ],
        'settings' => [
            'index' => [
                'title' => 'الاعدادات',
                'page_title' => 'الاعدادات',
                'widget' => [
                    'title' => 'ويدجيت واتساب',
                    'mobile' => 'رقم الهاتف',
                    'message' => 'الرسالة',
                    'color' => 'اللون',
                ],
                'password' => [
                    'title' => 'كلمة السر',
                    'current_password' => 'كلمة السر الحالية',
                    'new_password' => 'كلمة السر الجديدة',
                    'new_password_confirmation' => 'تآكيد كلمة السر الجديدة',
                    'updated' => 'تم تعديل كلمة السر بنجاح',
                ],
            ],
            'columns' => [
                'key' => 'الاسم',
                'value' => 'القيمة',
            ],
        ],
    ],
];
