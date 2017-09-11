<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <!--[if !mso]><!-->
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <!--<![endif]-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - {{ \Acelle\Model\Setting::get("site_name") }}</title>
    <link href="{{ URL::asset('assets/css/icons/icomoon/styles.css') }}" rel="stylesheet" type="text/css">

    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/builder.css') }}" />
    <!--[if (gte mso 9)|(IE)]>
    <style type="text/css">
        table {border-collapse: collapse;}
    </style>
    <![endif]-->

    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

    <script type="text/javascript" src="{{ URL::asset('tinymce/tinymce.min.js') }}"></script>

    <script type="text/javascript" src="{{ URL::asset('js/editor.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('assets/js/plugins/forms/styling/uniform.min.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('assets/js/plugins/forms/validation/validate.min.js') }}"></script>

    <script type="text/javascript" src="{{ URL::asset('js/colorpicker/js/colorpicker.js') }}"></script>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('js/colorpicker/css/colorpicker.css') }}" />

    <script type="text/javascript" src="{{ URL::asset('js/validate.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('js/builder.js') }}"></script>

    @include('layouts._script_vars')

</head>
<body class="builder-page">
    <topbar>
        @yield('content')
    </topbar>

    <div title="{{ trans('messages.remove_row') }}" class="remove_block">
        <i class="icon-cross2"></i>
    </div>

    <table class="hide">
        <tr class="block full_width_banner">
            <td>
                <table class="" align="center">
                    <tr>
                        <td class="full-width-image">
                            <img src="{{ url("images/icons/image_placeholder_wide.png") }}" width="600" alt="" />
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr class="block single_text_row">
            <td class="one-column">
                <table width="100%">
                    <tr>
                        <td class="inner contents">
                            <p class="h1">Lorem ipsum dolor sit amet</p>
                            <p>Maecenas sed ante pellentesque, posuere leo id, eleifend dolor. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Praesent laoreet malesuada cursus. Maecenas scelerisque congue eros eu posuere. Praesent in felis ut velit pretium lobortis rhoncus ut erat.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr class="block two_image_text_columns">
            <td class="two-column">
                <!--[if (gte mso 9)|(IE)]>
                <table width="100%">
                <tr>
                <td width="50%" valign="top">
                <![endif]-->
                <div class="column">
                    <table width="100%">
                        <tr>
                            <td class="inner">
                                <table class="contents">
                                    <tr>
                                        <td>
                                            <img src="{{ url("images/icons/image_placeholder_medium.png") }}" width="280" alt="" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text">
                                            <p>Maecenas sed ante pellentesque, posuere leo id, eleifend dolor. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos.</p>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </div>
                <!--[if (gte mso 9)|(IE)]>
                </td><td width="50%" valign="top">
                <![endif]-->
                <div class="column">
                    <table width="100%">
                        <tr>
                            <td class="inner">
                                <table class="contents">
                                    <tr>
                                        <td>
                                            <img src="{{ url("images/icons/image_placeholder_medium.png") }}" width="280" alt="" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text">
                                            <p>Maecenas sed ante pellentesque, posuere leo id, eleifend dolor. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. </p>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </div>
                <!--[if (gte mso 9)|(IE)]>
                </td>
                </tr>
                </table>
                <![endif]-->
            </td>
        </tr>
        <tr class="block three_image_text_columns">
            <td class="three-column">
                <!--[if (gte mso 9)|(IE)]>
                <table width="100%">
                <tr>
                <td width="200" valign="top">
                <![endif]-->
                <div class="column">
                    <table width="100%">
                        <tr>
                            <td class="inner">
                                <table class="contents">
                                    <tr>
                                        <td>
                                            <img src="{{ url("images/icons/image_placeholder_square.png") }}" width="180" alt="" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text">
                                            <p>Scelerisque congue eros eu posuere. Praesent in felis ut velit pretium lobortis rhoncus ut erat.</p>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </div>
                <!--[if (gte mso 9)|(IE)]>
                </td><td width="200" valign="top">
                <![endif]-->
                <div class="column">
                    <table width="100%">
                        <tr>
                            <td class="inner">
                                <table class="contents">
                                    <tr>
                                        <td>
                                            <img src="{{ url("images/icons/image_placeholder_square.png") }}" width="180" alt="" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text">
                                            <p>Scelerisque congue eros eu posuere. Praesent in felis ut velit pretium lobortis rhoncus ut erat.</p>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </div>
                <!--[if (gte mso 9)|(IE)]>
                </td><td width="200" valign="top">
                <![endif]-->
                <div class="column">
                    <table width="100%">
                        <tr>
                            <td class="inner">
                                <table class="contents">
                                    <tr>
                                        <td>
                                            <img src="{{ url("images/icons/image_placeholder_square.png") }}" width="180" alt="" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text">
                                            <p>Scelerisque congue eros eu posuere. Praesent in felis ut velit pretium lobortis rhoncus ut erat.</p>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </div>
                <!--[if (gte mso 9)|(IE)]>
                </td>
                </tr>
                </table>
                <![endif]-->
            </td>
        </tr>
        <tr class="block list_image_left">
            <td class="left-sidebar">
                <!--[if (gte mso 9)|(IE)]>
                <table width="100%">
                <tr>
                <td width="100">
                <![endif]-->
                <div class="column left">
                    <table width="100%">
                        <tr>
                            <td class="inner">
                                <img src="{{ url("images/icons/image_placeholder_square.png") }}" width="80" alt="" />
                            </td>
                        </tr>
                    </table>
                </div>
                <!--[if (gte mso 9)|(IE)]>
                </td><td width="500">
                <![endif]-->
                <div class="column right">
                    <table width="100%">
                        <tr>
                            <td class="inner contents">
                                <p>Scelerisque congue eros eu posuere. Praesent in felis ut velit pretium lobortis rhoncus ut erat.</p>Praesent laoreet malesuada cursus. Maecenas scelerisque congue eros eu posuere. Praesent in felis ut velit pretium lobortis rhoncus ut erat. <a href="#">Read&nbsp;on</a></p>
                            </td>
                        </tr>
                    </table>
                </div>
                <!--[if (gte mso 9)|(IE)]>
                </td>
                </tr>
                </table>
                <![endif]-->
            </td>
        </tr>
        <tr class="block list_image_right">
            <td class="right-sidebar" dir="rtl">
                <!--[if (gte mso 9)|(IE)]>
                <table width="100%" dir="rtl">
                <tr>
                <td width="100">
                <![endif]-->
                <div class="column left" dir="ltr">
                    <table width="100%">
                        <tr>
                            <td class="inner contents">
                                <img src="{{ url("images/icons/image_placeholder_square.png") }}" width="80" alt="" />
                            </td>
                        </tr>
                    </table>
                </div>
                <!--[if (gte mso 9)|(IE)]>
                </td><td width="500">
                <![endif]-->
                <div class="column right" dir="ltr">
                    <table width="100%">
                        <tr>
                            <td class="inner contents">
                                <p>Maecenas sed ante pellentesque, posuere leo id, eleifend dolor. Class aptent taciti sociosqu ad litora torquent per conubia nostra. <a href="#">Per&nbsp;inceptos</a></p>
                            </td>
                        </tr>
                    </table>
                </div>
                <!--[if (gte mso 9)|(IE)]>
                </td>
                </tr>
                </table>
                <![endif]-->
            </td>
        </tr>
        <tr class="block three_heading_columns">
            <td class="three-column">
                <!--[if (gte mso 9)|(IE)]>
                <table width="100%">
                <tr>
                <td width="200" valign="top">
                <![endif]-->
                <div class="column">
                    <table width="100%">
                        <tr>
                            <td class="inner contents">
                                <p class="h2">Heading</p>
                                <p>Class eleifend aptent taciti sociosqu ad litora torquent conubia</p>
                                <p><a href="#">Read more</a></p>
                            </td>
                        </tr>
                    </table>
                </div>
                <!--[if (gte mso 9)|(IE)]>
                </td><td width="200" valign="top">
                <![endif]-->
                <div class="column">
                    <table width="100%">
                        <tr>
                            <td class="inner contents">
                                <p class="h2">Heading</p>
                                <p>Class eleifend aptent taciti sociosqu ad litora torquent conubia</p>
                                <p><a href="#">Read more</a></p>
                            </td>
                        </tr>
                    </table>
                </div>
                <!--[if (gte mso 9)|(IE)]>
                </td><td width="200" valign="top">
                <![endif]-->
                <div class="column">
                    <table width="100%">
                        <tr>
                            <td class="inner contents">
                                <p class="h2">Heading</p>
                                <p>Class eleifend aptent taciti sociosqu ad litora torquent conubia</p>
                                <p><a href="#">Read more</a></p>
                            </td>
                        </tr>
                    </table>
                </div>
                <!--[if (gte mso 9)|(IE)]>
                </td>
                </tr>
                </table>
                <![endif]-->
            </td>
        </tr>
        <tr class="block footer">
            <td class="one-column">
                <table width="100%">
                    <tr>
                        <td class="inner contents">
                            <hr>
                            <p>
                                <em>Copyright &copy; {CONTACT_NAME}, All rights reserved.</em><br />
                                <br />
                                <strong>Our mailing address is:</strong><br />
                                <a href="mailto:{CONTACT_EMAIL}">{CONTACT_EMAIL}</a><br />
                                <br />
                                Want to change how you receive these emails?<br />
                                You can <a href="{UPDATE_PROFILE_URL}">update your preferences</a> or <a href="{UNSUBSCRIBE_URL}">unsubscribe from this list</a>
                            </p>
                            <hr>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr class="block image_right">
            <td class="right-image" dir="rtl">
                <!--[if (gte mso 9)|(IE)]>
                <table width="100%" dir="rtl">
                <tr>
                <td width="245">
                <![endif]-->
                <div class="column left" dir="ltr">
                    <table width="100%">
                        <tr>
                            <td class="inner contents">
                                <img src="{{ url("images/icons/image_placeholder_medium.png") }}" width="220" alt="" />
                            </td>
                        </tr>
                    </table>
                </div>
                <!--[if (gte mso 9)|(IE)]>
                </td><td width="350">
                <![endif]-->
                <div class="column right" dir="ltr">
                    <table width="100%">
                        <tr>
                            <td class="inner contents">
                                <p class="h2">Heading</p>
                                <p>Maecenas sed ante pellentesque, posuere leo id, eleifend dolor. Class aptent taciti sociosqu ad litora torquent per conubia nostra. <a href="#">Per&nbsp;inceptos</a></p>
                            </td>
                        </tr>
                    </table>
                </div>
                <!--[if (gte mso 9)|(IE)]>
                </td>
                </tr>
                </table>
                <![endif]-->
            </td>
        </tr>
        <tr class="block image_left">
            <td class="left-image">
                <!--[if (gte mso 9)|(IE)]>
                <table width="100%">
                <tr>
                <td width="245">
                <![endif]-->
                <div class="column left">
                    <table width="100%">
                        <tr>
                            <td class="inner contents">
                                <img src="{{ url("images/icons/image_placeholder_medium.png") }}" width="220" alt="" />
                            </td>
                        </tr>
                    </table>
                </div>
                <!--[if (gte mso 9)|(IE)]>
                </td><td width="350">
                <![endif]-->
                <div class="column right">
                    <table width="100%">
                        <tr>
                            <td class="inner contents">
                                <p class="h2">Heading</p>
                                <p>Maecenas sed ante pellentesque, posuere leo id, eleifend dolor. Class aptent taciti sociosqu ad litora torquent per conubia nostra. <a href="#">Per&nbsp;inceptos</a></p>
                            </td>
                        </tr>
                    </table>
                </div>
                <!--[if (gte mso 9)|(IE)]>
                </td>
                </tr>
                </table>
                <![endif]-->
            </td>
        </tr>
        <tr class="block button">
            <td class="button-row">
                <a href="#no-link" style="padding-top:15px; padding-right:30px; padding-bottom:15px; padding-left:30px;border-radius:3px; background-color:#6DC6DD;color:#FFFFFF; font-family:Helvetica, Arial, sans-serif; font-size:16px; font-weight:bold; text-decoration:none;">Button Text</a>
            </td>
        </tr>
    </table>

    <toolbox>
        <div class="left-box">
            <div class="block tool-item" template="full_width_banner">
                <i class="icon-file-picture"></i>
                <label>{{ trans('messages.full_width_banner') }}</label>
            </div>
            <div class="block tool-item" template="single_text_row">
                <i class="icon-paragraph-left2"></i>
                <label>{{ trans('messages.single_text_row') }}</label>
            </div>
            <div class="block tool-item" template="two_image_text_columns">
                <i class="icon-newspaper2"></i>
                <label>{{ trans('messages.two_image_text_columns') }}</label>
            </div>
            <div class="block tool-item" template="three_image_text_columns">
                <i class="icon-stack-picture"></i>
                <label>{{ trans('messages.three_image_text_columns') }}</label>
            </div>
            <div class="block tool-item" template="list_image_left">
                <i class="icon-list"></i>
                <label>{{ trans('messages.list_image_left') }}</label>
            </div>
            <div class="block tool-item" template="list_image_right">
                <i class="icon-list" style="
                    -ms-transform: rotate(180deg); /* IE 9 */
                    -webkit-transform: rotate(180deg); /* Chrome, Safari, Opera */
                    transform: rotate(180deg);"></i>
                <label>{{ trans('messages.list_image_right') }}</label>
            </div>
            <div class="block tool-item" template="three_heading_columns">
                <i class="icon-stack-text"></i>
                <label>{{ trans('messages.three_heading_columns') }}</label>
            </div>
            <div class="block tool-item" template="footer">
                <i class="icon-ipad"></i>
                <label>{{ trans('messages.footer') }}</label>
            </div>
            <div class="block tool-item" template="image_right">
                <i class="icon-newspaper" style="
                    -ms-transform: rotate(180deg); /* IE 9 */
                    -webkit-transform: rotate(180deg); /* Chrome, Safari, Opera */
                    transform: rotate(180deg);"></i>
                <label>{{ trans('messages.image_right') }}</label>
            </div>
            <div class="block tool-item" template="image_left">
                <i class="icon-newspaper"></i>
                <label>{{ trans('messages.image_left') }}</label>
            </div>
            <!--<div class="block tool-item" template="button">
                <i class="icon-newspaper"></i>
                <label>{{ trans('messages.button') }}</label>
            </div>-->
        </div>
    </toolbox>

    <editor>
        <div style="float: right">
            <button class="btn bg-slate close-editor"><i class="icon-cross2"></i></button>
        </div>

        <div class="tab">
            <ul>
                <li class="active">
                    <a href="#builder"><i class="icon-pencil5"></i> {{ trans('messages.builder') }}</a>
                </li>
                <li>
                    <a href="#design"><i class="icon-toggle"></i> {{ trans('messages.row_style') }}</a>
                </li>
            </ul>

            <div class="tab-container">
                <div class="tab-pane active" id="builder">
                    <textarea class="builder-editor pull-right"></textarea>
                    <br />
                    @include('elements._tags', ['tags' => Acelle\Model\Template::tags((isset($list) ? $list : null))])
                </div>
                <div class="tab-pane" id="design">
                    <div class="form-group">
                        <label>{{ trans('messages.row_background') }}</label>
                        <span class="color" id="background-color"></span>
                    </div>
                    <div class="form-group">
                        <label>{{ trans('messages.text_color') }}</label>
                        <span class="color" id="text-color"></span>
                    </div>
                    <div class="form-group">
                        <label>{{ trans('messages.padding') }}</label>
                        <table>
                            <tr>
                                <td><input type="text" name="padding-top" placeholder="{{ trans('messages.top') }}" /></td>
                                <td><input type="text" name="padding-right" placeholder="{{ trans('messages.right') }}" /></td>
                                <td><input type="text" name="padding-bottom" placeholder="{{ trans('messages.bottom') }}" /></td>
                                <td><input type="text" name="padding-left" placeholder="{{ trans('messages.left') }}" /></td>
                            </tr>
                        </table>
                    </div>
                    <div class="form-group">
                        <label>{{ trans('messages.margin') }}</label>
                        <table>
                            <tr>
                                <td><input type="text" name="margin-top" placeholder="{{ trans('messages.top') }}" /></td>
                                <td><input type="text" name="margin-right" placeholder="{{ trans('messages.right') }}" /></td>
                                <td><input type="text" name="margin-bottom" placeholder="{{ trans('messages.bottom') }}" /></td>
                                <td><input type="text" name="margin-left" placeholder="{{ trans('messages.left') }}" /></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </editor>

    <leftbar>
        <div class="container">
            <h1>{{ trans('messages.elements') }}</h1>
            <div class="toolbox-container">

            </div>
        </div>
    </leftbar>

    @yield('template_content')

</body>
</html>
