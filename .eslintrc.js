/*
 * https://eslint.org/docs/rules/
 * https://eslint.vuejs.org/rules/
 */
module.exports = {
    env: {
        browser: true, es2021: true, node: true
    },
    overrides: [{files: ['*.vue'], rules: {indent: 'off'}}],
    parser: require.resolve('vue-eslint-parser'),
    parserOptions: {
        ecmaVersion: 2021,
        extraFileExtensions: ['.vue'],
        parser: '@babel/eslint-parser',
        sourceType: 'module'
    },
    plugins: ['eslint-plugin-vue'],
    root: true,
    rules: {
        'accessor-pairs': 'error',
        'array-bracket-newline': ['error', 'consistent'],
        'array-bracket-spacing': 'error',
        'array-callback-return': ['error', {checkForEach: true}],
        'array-element-newline': ['error', 'consistent'],
        'arrow-body-style': 'error',
        'arrow-parens': ['error', 'as-needed'],
        'arrow-spacing': 'error',
        'block-spacing': 'error',
        'brace-style': 'error',
        camelcase: 'error',
        'class-methods-use-this': 'error',
        'comma-dangle': ['error', 'never'],
        'comma-spacing': 'error',
        'comma-style': 'error',
        'computed-property-spacing': 'error',
        'consistent-return': 'error',
        'consistent-this': 'error',
        'constructor-super': 'error',
        'default-case-last': 'error',
        'default-param-last': 'error',
        'dot-location': ['error', 'property'],
        'dot-notation': 1, //['error', {allowKeywords: false}],
        'eol-last': 'error',
        eqeqeq: 'error',
        'for-direction': 'error',
        'func-call-spacing': 'error',
        'func-style': ['error', 'declaration', {allowArrowFunctions: true}],
        'function-call-argument-newline': ['error', 'consistent'],
        'function-paren-newline': ['error', 'consistent'],
        'getter-return': 'error',
        'grouped-accessor-pairs': ['error', 'getBeforeSet'],
        indent: ['error', 4],
        'init-declarations': 'error',
        'key-spacing': 'error',
        'keyword-spacing': 'error',
        'linebreak-style': 'error',
        'lines-between-class-members': ['error', 'always', {exceptAfterSingleLine: true}],
        'max-classes-per-file': 'error',
        'new-cap': 'error',
        'new-parens': 'error',
        'no-alert': 'off',
        'no-array-constructor': 'error',
        'no-async-promise-executor': 'error',
        'no-await-in-loop': 'error',
        'no-bitwise': 'error',
        'no-caller': 'error',
        'no-case-declarations': 'error',
        'no-class-assign': 'error',
        'no-compare-neg-zero': 'error',
        'no-cond-assign': ['error', 'always'],
        'no-confusing-arrow': 'error',
        // 'no-console': [process.env.NODE_ENV === 'production' ? 'error' : 'off', {allow: ['error']}],
        'no-const-assign': 'error',
        'no-constant-condition': 'error',
        'no-constructor-return': 'error',
        'no-continue': 'off',
        'no-control-regex': 'error',
        'no-debugger': process.env.NODE_ENV === 'production' ? 'error' : 'off',
        'no-delete-var': 'error',
        'no-div-regex': 'error',
        'no-dupe-args': 'error',
        'no-dupe-else-if': 'error',
        'no-dupe-keys': 'error',
        'no-duplicate-case': 'error',
        'no-duplicate-imports': ['error', {includeExports: true}],
        'no-else-return': ['error', {allowElseIf: false}],
        'no-empty': 'error',
        'no-empty-character-class': 'error',
        'no-empty-function': 'error',
        'no-empty-pattern': 'error',
        'no-eval': 'error',
        'no-ex-assign': 'error',
        'no-extend-native': 'error',
        'no-extra-bind': 'error',
        'no-extra-boolean-cast': 'error',
        'no-extra-label': 'error',
        'no-extra-parens': ['error', 'all', {enforceForArrowConditionals: false}],
        'no-extra-semi': 'error',
        'no-fallthrough': 'error',
        'no-floating-decimal': 'error',
        'no-func-assign': 'error',
        'no-global-assign': 'error',
        'no-implicit-coercion': 'error',
        'no-implied-eval': 'error',
        'no-import-assign': 'error',
        'no-inner-declarations': 'off',
        'no-invalid-regexp': 'error',
        'no-invalid-this': 'error',
        'no-irregular-whitespace': ['error', {
            skipComments: false, skipRegExps: false, skipStrings: false, skipTemplates: false
        }],
        'no-iterator': 'error',
        'no-label-var': 'error',
        'no-labels': 'error',
        'no-lone-blocks': 'error',
        'no-lonely-if': 'error',
        'no-loop-func': 'error',
        'no-loss-of-precision': 'error',
        'no-magic-numbers': 'off',
        'no-misleading-character-class': 'error',
        'no-mixed-spaces-and-tabs': 'error',
        'no-multi-assign': 'error',
        'no-multi-spaces': 'error',
        'no-multi-str': 'error',
        'no-multiple-empty-lines': ['error', {max: 1, maxBOF: 0, maxEOF: 1}],
        'no-negated-condition': 'error',
        'no-nested-ternary': 'error',
        'no-new': 'error',
        'no-new-func': 'error',
        'no-new-object': 'error',
        'no-new-symbol': 'error',
        'no-new-wrappers': 'error',
        'no-nonoctal-decimal-escape': 'error',
        'no-obj-calls': 'error',
        'no-octal': 'error',
        'no-octal-escape': 'error',
        'no-param-reassign': 'error',
        'no-promise-executor-return': 'error',
        'no-proto': 'error',
        'no-prototype-builtins': 'error',
        'no-redeclare': 'error',
        'no-regex-spaces': 'error',
        'no-return-assign': ['error', 'always'],
        'no-return-await': 'error',
        'no-script-url': 'error',
        'no-self-assign': 'error',
        'no-self-compare': 'error',
        'no-sequences': ['error', {allowInParentheses: false}],
        'no-setter-return': 'error',
        'no-shadow': ['error', {hoist: 'all'}],
        'no-shadow-restricted-names': 'error',
        'no-sparse-arrays': 'error',
        'no-tabs': 'error',
        'no-template-curly-in-string': 'error',
        'no-this-before-super': 'error',
        'no-trailing-spaces': 'error',
        'no-undef': 'off',
        'no-undef-init': 'error',
        'no-undefined': 'error',
        'no-underscore-dangle': ['error', {allowFunctionParams: false}],
        'no-unexpected-multiline': 'error',
        'no-unmodified-loop-condition': 'error',
        'no-unneeded-ternary': 'error',
        'no-unreachable': 'error',
        'no-unreachable-loop': 'error',
        'no-unsafe-finally': 'error',
        'no-unsafe-negation': 'error',
        'no-unsafe-optional-chaining': 'error',
        'no-unused-expressions': 'error',
        'no-unused-labels': 'error',
        'no-unused-private-class-members': 'error',
        'no-unused-vars': 'error',
        'no-use-before-define': ['error', {variables: false}],
        'no-useless-backreference': 'error',
        'no-useless-call': 'error',
        'no-useless-catch': 'error',
        'no-useless-computed-key': ['error', {enforceForClassMembers: true}],
        'no-useless-concat': 'error',
        'no-useless-constructor': 'error',
        'no-useless-escape': 'error',
        'no-useless-rename': 'error',
        'no-useless-return': 'error',
        'no-var': 'error',
        'no-void': 'error',
        'no-whitespace-before-property': 'error',
        'no-with': 'error',
        'object-curly-newline': ['error', {consistent: true}],
        'object-curly-spacing': 'error',
        'object-shorthand': 'error',
        'operator-assignment': 'error',
        'operator-linebreak': ['error', 'before'],
        'padded-blocks': ['error', 'never'],
        'prefer-arrow-callback': 'error',
        'prefer-const': 'error',
        'prefer-object-spread': 'error',
        'prefer-regex-literals': ['error', {disallowRedundantWrapping: true}],
        'prefer-rest-params': 'error',
        'prefer-spread': 'error',
        'prefer-template': 'error',
        'quote-props': ['error', 'as-needed'],
        quotes: ['error', 'single', {avoidEscape: false}],
        'require-atomic-updates': 'error',
        'rest-spread-spacing': 'error',
        semi: ['error', 'never'],
        'sort-imports': 'error',
        'sort-keys': 'error',
        'sort-vars': 'error',
        'space-before-function-paren': ['error', {
            anonymous: 'always', asyncArrow: 'always', named: 'never'
        }],
        'space-in-parens': 'error',
        'space-infix-ops': 'error',
        'space-unary-ops': 'error',
        strict: 'error',
        'switch-colon-spacing': 'error',
        'template-curly-spacing': 'error',
        'use-isnan': 'error',
        'valid-typeof': 'error',
        'vue/array-bracket-newline': ['error', 'consistent'],
        'vue/array-bracket-spacing': 'error',
        'vue/arrow-spacing': 'error',
        'vue/attribute-hyphenation': 'error',
        'vue/attributes-order': 'error',
        'vue/block-lang': ['error', {script: {allowNoLang: true}}],
        'vue/block-spacing': 'error',
        'vue/block-tag-newline': ['error', {
            maxEmptyLines: 0, multiline: 'always', singleline: 'always'
        }],
        'vue/brace-style': 'error',
        'vue/camelcase': 'error',
        'vue/comma-dangle': ['error', 'never'],
        'vue/comma-spacing': 'error',
        'vue/comma-style': 'error',
        'vue/component-api-style': ['error', ['script-setup']],
        'vue/component-definition-name-casing': 'error',
        'vue/component-name-in-template-casing': ['error', 'PascalCase', {
            ignores: ['component'], registeredComponentsOnly: false
        }],
        'vue/component-options-name-casing': 'error',
        'vue/component-tags-order': ['error', {order: ['script', 'template', 'style']}],
        'vue/custom-event-name-casing': ['error', 'camelCase'],
        'vue/dot-location': ['error', 'property'],
        'vue/dot-notation': 1, //['error', {allowKeywords: false}],
        'vue/eqeqeq': 'error',
        'vue/first-attribute-linebreak': 'error',
        'vue/func-call-spacing': 'error',
        'vue/html-closing-bracket-newline': ['error', {
            multiline: 'never', singleline: 'never'
        }],
        'vue/html-closing-bracket-spacing': ['error', {
            endTag: 'never', selfClosingTag: 'never', startTag: 'never'
        }],
        'vue/html-end-tags': 'error',
        'vue/html-indent': ['error', 4, {
            alignAttributesVertically: true, attribute: 1, baseIndent: 1, closeBracket: 0
        }],
        'vue/html-quotes': 'error',
        'vue/html-self-closing': ['error', {
            html: {
                component: 'always', normal: 'always', void: 'always'
            }, math: 'always', svg: 'always'
        }],
        'vue/key-spacing': 'error',
        'vue/keyword-spacing': 'error',
        'vue/multi-word-component-names': 'error',
        'vue/multiline-html-element-content-newline': 'error',
        'vue/mustache-interpolation-spacing': 'error',
        'vue/next-tick-style': 'error',
        'vue/no-arrow-functions-in-watch': 'error',
        'vue/no-async-in-computed-properties': 'error',
        'vue/no-boolean-default': 'error',
        'vue/no-child-content': 'error',
        'vue/no-computed-properties-in-data': 'error',
        'vue/no-constant-condition': 'error',
        'vue/no-deprecated-data-object-declaration': 'error',
        'vue/no-deprecated-destroyed-lifecycle': 'error',
        'vue/no-deprecated-dollar-listeners-api': 'error',
        'vue/no-deprecated-dollar-scopedslots-api': 'error',
        'vue/no-deprecated-events-api': 'error',
        'vue/no-deprecated-filter': 'error',
        'vue/no-deprecated-functional-template': 'error',
        'vue/no-deprecated-html-element-is': 'error',
        'vue/no-deprecated-inline-template': 'error',
        'vue/no-deprecated-props-default-this': 'error',
        'vue/no-deprecated-router-link-tag-prop': 'error',
        'vue/no-deprecated-scope-attribute': 'error',
        'vue/no-deprecated-slot-attribute': 'error',
        'vue/no-deprecated-slot-scope-attribute': 'error',
        'vue/no-deprecated-v-bind-sync': 'error',
        'vue/no-deprecated-v-is': 'error',
        'vue/no-deprecated-v-on-native-modifier': 'error',
        'vue/no-deprecated-v-on-number-modifiers': 'error',
        'vue/no-deprecated-vue-config-keycodes': 'error',
        'vue/no-dupe-keys': 'error',
        'vue/no-dupe-v-else-if': 'error',
        'vue/no-duplicate-attr-inheritance': 'error',
        'vue/no-duplicate-attributes': 'error',
        'vue/no-empty-component-block': 'error',
        'vue/no-empty-pattern': 'error',
        'vue/no-export-in-script-setup': 'error',
        'vue/no-expose-after-await': 'off',
        'vue/no-extra-parens': ['error', 'all', {enforceForArrowConditionals: false}],
        'vue/no-invalid-model-keys': 'error',
        'vue/no-irregular-whitespace': ['error', {
            skipComments: false, skipRegExps: false, skipStrings: false, skipTemplates: false
        }],
        'vue/no-lifecycle-after-await': 'error',
        'vue/no-lone-template': 'error',
        'vue/no-loss-of-precision': 'error',
        'vue/no-multi-spaces': 'error',
        'vue/no-multiple-objects-in-class': 'error',
        'vue/no-multiple-slot-args': 'error',
        'vue/no-mutating-props': 'off',
        'vue/no-parsing-error': 'error',
        'vue/no-ref-as-operand': 'error',
        'vue/no-reserved-component-names': ['error', {
            disallowVue3BuiltInComponents: true, disallowVueBuiltInComponents: true
        }],
        'vue/no-reserved-keys': 'error',
        'vue/no-reserved-props': 'error',
        'vue/no-setup-props-destructure': 'error',
        'vue/no-shared-component-data': 'error',
        'vue/no-side-effects-in-computed-properties': 'error',
        'vue/no-spaces-around-equal-signs-in-attribute': 'error',
        'vue/no-sparse-arrays': 'error',
        'vue/no-static-inline-styles': 'error',
        'vue/no-template-key': 'error',
        'vue/no-template-shadow': 'error',
        'vue/no-textarea-mustache': 'error',
        'vue/no-this-in-before-route-enter': 'error',
        'vue/no-undef-properties': 'error',
        'vue/no-unused-components': 'error',
        'vue/no-unused-properties': 'error',
        'vue/no-unused-refs': 'error',
        'vue/no-unused-vars': 'error',
        'vue/no-use-computed-property-like-method': 'error',
        'vue/no-use-v-if-with-v-for': 'error',
        'vue/no-useless-concat': 'error',
        'vue/no-useless-mustaches': 'error',
        'vue/no-useless-template-attributes': 'error',
        'vue/no-useless-v-bind': 'error',
        'vue/no-v-for-template-key-on-child': 'error',
        'vue/no-v-html': 'error',
        'vue/no-v-text': 'error',
        'vue/no-watch-after-await': 'error',
        'vue/object-curly-newline': ['error', {consistent: true}],
        'vue/object-curly-spacing': 'error',
        'vue/one-component-per-file': 'error',
        'vue/operator-linebreak': ['error', 'before'],
        'vue/order-in-components': 'error',
        'vue/padding-line-between-blocks': 'error',
        'vue/prefer-separate-static-class': 'error',
        'vue/prefer-template': 'error',
        'vue/prop-name-casing': 'error',
        'vue/require-component-is': 'error',
        'vue/require-default-prop': 'error',
        'vue/require-emit-validator': 'off',
        'vue/require-explicit-emits': 1, //'error',
        'vue/require-prop-type-constructor': 'error',
        'vue/require-prop-types': 'off',
        'vue/require-render-return': 'error',
        'vue/require-slots-as-functions': 'error',
        'vue/require-toggle-inside-transition': 'error',
        'vue/require-v-for-key': 'error',
        'vue/require-valid-default-prop': 'error',
        'vue/return-in-computed-property': 'error',
        'vue/return-in-emits-validator': 'error',
        'vue/script-indent': ['error', 4, {baseIndent: 1, switchCase: 1}],
        'vue/script-setup-uses-vars': 'error',
        'vue/singleline-html-element-content-newline': 'error',
        'vue/sort-keys': 'error',
        'vue/space-in-parens': 'error',
        'vue/space-infix-ops': 'error',
        'vue/space-unary-ops': 'error',
        'vue/static-class-names-order': 'error',
        'vue/template-curly-spacing': 'error',
        'vue/this-in-template': 'error',
        'vue/use-v-on-exact': 'error',
        'vue/v-bind-style': 'error',
        'vue/v-for-delimiter-style': 'error',
        'vue/v-on-event-hyphenation': 'error',
        'vue/v-on-function-call': 'error',
        'vue/v-on-style': 'error',
        'vue/v-slot-style': ['error', {atComponent: 'shorthand'}],
        'vue/valid-define-emits': 'error',
        'vue/valid-define-props': 'error',
        'vue/valid-next-tick': 'error',
        'vue/valid-template-root': 'error',
        'vue/valid-v-bind': 'error',
        'vue/valid-v-cloak': 'error',
        'vue/valid-v-else': 'error',
        'vue/valid-v-else-if': 'error',
        'vue/valid-v-for': 'error',
        'vue/valid-v-html': 'error',
        'vue/valid-v-if': 'error',
        'vue/valid-v-is': 'error',
        'vue/valid-v-memo': 'error',
        'vue/valid-v-model': 'error',
        'vue/valid-v-on': 'error',
        'vue/valid-v-once': 'error',
        'vue/valid-v-pre': 'error',
        'vue/valid-v-show': 'error',
        'vue/valid-v-slot': 'error',
        'vue/valid-v-text': 'error'
    }
}
