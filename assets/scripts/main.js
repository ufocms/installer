/**
 * Copyright (c) 2024 UFOCMS
 *
 * This software is licensed under the GPLv3 license.
 * See the LICENSE file for more information.
 */

$.json2html = function (json) {
    const newTN = (text) => document.createTextNode(text);
    const newEL = (tag, attr) => {
        const EL = document.createElement(typeof tag === "undefined" ? "div" : (tag.length === 0 ? "div" : tag));
        if (attr) Object.entries(attr).forEach(([k, v]) => EL.setAttribute(k, v));
        return EL;
    };
    const JSON2DF = (data, PAR = new DocumentFragment()) => {
        const CH = typeof data === "string" ? newTN(data) : newEL(data.tag, data.attrs)
        if (data.html) {
            if (Array.isArray(data.html)) {
                data.html.forEach(d => JSON2DF(d, CH));
            } else {
                JSON2DF(data.html, CH);
            }
        }
        PAR.append(CH);
        return PAR;
    };
    const fakeElement = $(`<div></div>`);
    fakeElement.html(JSON2DF(json));
    return fakeElement.html();
};

(function () {
    const steps = {
        i18n: {
            title: i18n["Choose language"],
            html: [
                {
                    tag: "select",
                    html: function () {
                        let options = [];

                        $.each(allI18n, (key, value) => {
                            value = value.replace(".json", "");

                            const attrs = {value};

                            if (value === $("html").attr("lang"))
                                attrs["selected"] = true;

                            options.push({
                                tag: "option",
                                html: [key],
                                attrs: attrs
                            })
                        });

                        return options;
                    }(),
                    attrs: {
                        name: "i18n",
                        style: "margin-bottom: 10px"
                    }
                }
            ]
        },
        download: {
            title: i18n["Download UFOCMS"],
            html: {
                tag: "div",
                html: [
                    {
                        tag: "label",
                        html: [
                            i18n["Please select the version you want :"],
                            {
                                tag: "select",
                                html: [
                                    {
                                        tag: "option",
                                        html: i18n["Latest stable version"],
                                        attrs: {
                                            value: "lts"
                                        }
                                    },
                                    {
                                        tag: "option",
                                        html: i18n["Developers"],
                                        attrs: {
                                            value: "dev"
                                        }
                                    }
                                ],
                                attrs: {
                                    name: "version",
                                    required: true,
                                    style: "margin: 10px 0"
                                }
                            }
                        ]
                    }
                ]
            }
        },
        info: {
            title: i18n["Website information"],
            tag: "div",
            html: [
                {
                    tag: "span",
                    html: [i18n["Title"]],
                    attrs: {
                        style: "margin-bottom: 5px;display: block"
                    }
                },
                {
                    tag: "input",
                    attrs: {
                        placeholder: i18n["Title"],
                        name: "web_title"
                    }
                },
                {
                    tag: "span",
                    html: [i18n["Admin page folder name"]],
                    attrs: {
                        style: "margin-bottom: 5px;display: block"
                    }
                },
                {
                    tag: "input",
                    attrs: {
                        value: "cms",
                        placeholder: i18n["Admin page folder name"],
                        name: "admin_path",
                        dir: "ltr"
                    }
                },
                {
                    tag: "label",
                    html: [
                        {
                            tag: "input",
                            attrs: {
                                type: "checkbox",
                                name: "seo"
                            }
                        },
                        {
                            tag: "span",
                            html: [
                                i18n["Prevent SEO of the site / prevent your website from being checked by browsers"]
                            ]
                        }
                    ]
                },
            ]
        },
        admin: {
            title: i18n["Manager information"],
            tag: "div",
            html: [
                {
                    tag: "span",
                    html: [i18n["Name"]],
                    attrs: {
                        style: "margin-bottom: 5px;display: block"
                    }
                },
                {
                    tag: "input",
                    attrs: {
                        required: true,
                        placeholder: i18n["Name"],
                        name: "name"
                    }
                },
                {
                    tag: "span",
                    html: [i18n["Last name"]],
                    attrs: {
                        style: "margin-bottom: 5px;display: block"
                    }
                },
                {
                    tag: "input",
                    attrs: {
                        required: true,
                        placeholder: i18n["Last name"],
                        name: "last_name"
                    }
                },
                {
                    tag: "span",
                    html: [i18n["Login name"]],
                    attrs: {
                        style: "margin-bottom: 5px;display: block"
                    }
                },
                {
                    tag: "input",
                    attrs: {
                        required: true,
                        placeholder: i18n["Login name"],
                        name: "login_name"
                    }
                },
                {
                    tag: "span",
                    html: [i18n["Email"]],
                    attrs: {
                        style: "margin-bottom: 5px;display: block"
                    }
                },
                {
                    tag: "input",
                    attrs: {
                        placeholder: i18n["Email"],
                        name: "email",
                        inputmode: "email"
                    }
                },
                {
                    tag: "span",
                    html: [i18n["Password"]],
                    attrs: {
                        style: "margin-bottom: 5px;display: block"
                    }
                },
                {
                    tag: "input",
                    attrs: {
                        required: true,
                        placeholder: i18n["Password"],
                        name: "password"
                    }
                },
            ]
        },
        database: {
            title: i18n["Configure database"],
            tag: "div",
            html: [
                {
                    tag: "span",
                    html: [i18n["Host name"]],
                    attrs: {
                        style: "margin-bottom: 5px;display: block"
                    }
                },
                {
                    tag: "input",
                    attrs: {
                        required: true,
                        value: "localhost",
                        placeholder: i18n["Host name"],
                        name: "hostname"
                    }
                },
                {
                    tag: "span",
                    html: [i18n["Port"] + ` (${i18n["Optional"]})`],
                    attrs: {
                        style: "margin-bottom: 5px;display: block"
                    }
                },
                {
                    tag: "input",
                    attrs: {
                        placeholder: i18n["Port"],
                        name: "port"
                    }
                },
                {
                    tag: "span",
                    html: [i18n["Database name"]],
                    attrs: {
                        style: "margin-bottom: 5px;display: block"
                    }
                },
                {
                    tag: "input",
                    attrs: {
                        required: true,
                        placeholder: i18n["Database name"],
                        name: "database"
                    }
                },
                {
                    tag: "span",
                    html: [i18n["Username"]],
                    attrs: {
                        style: "margin-bottom: 5px;display: block"
                    }
                },
                {
                    tag: "input",
                    attrs: {
                        required: true,
                        placeholder: i18n["Username"],
                        name: "username"
                    }
                },
                {
                    tag: "span",
                    html: [i18n["Password"]],
                    attrs: {
                        style: "margin-bottom: 5px;display: block"
                    }
                },
                {
                    tag: "input",
                    attrs: {
                        placeholder: i18n["Password"],
                        name: "password"
                    }
                },
                {
                    tag: "span",
                    html: [i18n["Tables name prefix"]],
                    attrs: {
                        style: "margin-bottom: 5px;display: block"
                    }
                },
                {
                    tag: "input",
                    attrs: {
                        required: true,
                        value: "ufo_",
                        placeholder: i18n["Tables name prefix"],
                        name: "prefix",
                        style: "margin-bottom: 20px;direction: ltr"
                    }
                },
                {
                    tag: "span",
                    html: [i18n["Collations"]],
                    attrs: {
                        style: "margin-bottom: 5px;display: block"
                    }
                },
                {
                    tag: "select",
                    html: function (groups = []) {
                        $.each(collations, (key, value) => {
                            groups.push({
                                tag: "optgroup",
                                html: function (options = []) {
                                    value.forEach(value => {
                                        const attrs = {
                                            value,
                                            style: "font-size:15"
                                        };

                                        if (value === "utf8mb4_general_ci")
                                            attrs["selected"] = true;

                                        options.push({
                                            tag: "option",
                                            html: [value],
                                            attrs
                                        })
                                    });
                                    return options
                                }(),
                                attrs: {
                                    label: key,
                                    style: "font-size:16px;display:block"
                                }
                            })
                        })
                        return groups
                    }(),
                    attrs: {
                        style: "text-align:left",
                        name: "collate"
                    }
                }
            ]
        },
        welcome: {
            title: "Welcome",
            html: [
                {
                    tag: "div",
                    html: [
                        {
                            tag: "div",
                            html: [i18n["Hi"]]
                        },
                        {
                            tag: "div",
                            html: [
                                {
                                    tag: "span",
                                    html: [
                                        i18n["Welcome to UFOCMS"]
                                    ],
                                    style: "margin: 0 10px;display: block"
                                }
                            ]
                        }
                    ],
                    attrs: {
                        class: "welcome"
                    }
                }
            ]
        }
    }, progress = {
        tag: "div",
        html: [
            {
                tag: "div",
                attrs: {
                    class: "progress-bar-value"
                }
            }
        ],
        attrs: {
            class: "progress-bar"
        }
    };

    function renderStep () {
        const $step = steps[step];

        $("body").html($.json2html({
            tag: "div",
            html: [
                {
                    tag: "header",
                    html: [{
                        tag: "h3",
                        html: [$step.title]
                    }]
                },
                {
                    tag: "form",
                    html: [
                        {
                            tag: "div",
                            html: $step.html,
                            attrs: {
                                class: "content"
                            }
                        },
                        {
                            tag: "div",
                            html: [
                                {
                                    tag: "button",
                                    html: [
                                        Object.keys(steps)[Object.keys(steps).length-2] === step ? i18n[
                                            "Setting up"
                                        ] : i18n["Save and continue"]
                                    ],
                                    attrs: {
                                        type: "submit",
                                        style: "margin-top: 10px"
                                    }
                                }
                            ],
                            attrs: {
                                style: "width: 100%"
                            }
                        }
                    ],
                    attrs: {
                        class: "step"
                    }
                }
            ],
            attrs: {
                class: "container"
            }
        }));

        if (step === "welcome") {
            setTimeout(() =>
                location.href = redirectTo
            , 6000);
            return;
        }

        $("form.step").submit(function (e) {
            e.preventDefault();

            $.ajax({
                url: location.href,
                data: `step=${step}&${$(this).serialize()}`,
                type: "POST",
                dataType: "json",
                beforeSend ( ) {
                    $(`form.step :where(.content, button[type="submit"])`).hide();
                    $("form.step").append($.json2html(
                        progress
                    ));
                },
                complete ( ) {
                    $(`form.step :where(.content, button[type="submit"])`).show();
                    $(".progress-bar").remove();
                },
                success (result) {
                    if (result.status === 200) {
                        if (typeof result.reload === "undefined")
                            return renderStep();

                        location.reload();
                    } else alert(result.message)
                },
                error: xhr => alert(i18n["Connection error"])
            })
        });
    }

    renderStep();
}());