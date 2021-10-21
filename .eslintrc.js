module.exports = {
    env: {
        browser: true,
        es2021: true,
        node: true
    },
    extends: [
        // https://eslint.vuejs.org/rules/
        'plugin:vue/vue3-recommended',
        // https://www.npmjs.com/package/@vue/eslint-config-typescript
        '@vue/typescript/recommended'
    ],
    overrides: [{files: ['*.vue'], rules: {'@typescript-eslint/indent': 'off'}}],
    parserOptions: {
        ecmaVersion: 2021,
        project: require('path').resolve('tsconfig.json')
    },
    root: true,
    rules: {
        // https://eslint.org/docs/rules/
        // Possible Errors
        'for-direction': 'error',
        'getter-return': 'error',
        'no-async-promise-executor': 'error',
        'no-await-in-loop': 'error',
        'no-compare-neg-zero': 'error',
        'no-cond-assign': 'error',
        'no-console': process.env.NODE_ENV === 'production' ? 'error' : 'off',
        'no-constant-condition': 'error',
        'no-control-regex': 'error',
        'no-debugger': process.env.NODE_ENV === 'production' ? 'error' : 'off',
        'no-dupe-args': 'error',
        'no-dupe-else-if': 'error',
        'no-dupe-keys': 'error',
        'no-duplicate-case': 'error',
        'no-empty': 'error',
        'no-empty-character-class': 'error',
        'no-ex-assign': 'error',
        'no-extra-boolean-cast': 'error',
        'no-func-assign': 'error',
        'no-import-assign': 'error',
        'no-inner-declarations': 'error',
        'no-invalid-regexp': 'error',
        'no-irregular-whitespace': 'error',
        'no-misleading-character-class': 'error',
        'no-obj-calls': 'error',
        'no-promise-executor-return': 'error',
        'no-prototype-builtins': 'error',
        'no-regex-spaces': 'error',
        'no-setter-return': 'error',
        'no-sparse-arrays': 'error',
        'no-template-curly-in-string': 'error',
        'no-unexpected-multiline': 'error',
        'no-unreachable': 'error',
        'no-unreachable-loop': 'error',
        'no-unsafe-finally': 'error',
        'no-unsafe-negation': 'error',
        'no-unsafe-optional-chaining': 'error',
        'no-useless-backreference': 'error',
        'require-atomic-updates': 'error',
        'use-isnan': 'error',
        'valid-typeof': 'error',
        // Best Practices
        'array-callback-return': 'error',
        'block-scoped-var': 'error',
        'class-methods-use-this': 'error',
        'complexity': 'error',
        'consistent-return': 'error',
        'default-case-last': 'error',
        'dot-location': ['error', 'property'],
        'eqeqeq': 'error',
        'grouped-accessor-pairs': 'error',
        'max-classes-per-file': 'error',
        'no-alert': 'error',
        'no-caller': 'error',
        'no-case-declarations': 'error',
        'no-constructor-return': 'error',
        'no-div-regex': 'error',
        'no-else-return': ['error', {allowElseIf: false}],
        'no-empty-pattern': 'error',
        'no-eval': 'error',
        'no-extend-native': 'error',
        'no-extra-bind': 'error',
        'no-fallthrough': 'error',
        'no-floating-decimal': 'error',
        'no-global-assign': 'error',
        'no-implicit-coercion': 'error',
        'no-implicit-globals': 'error',
        'no-labels': 'error',
        'no-lone-blocks': 'error',
        'no-multi-spaces': 'error',
        'no-multi-str': 'error',
        'no-new': 'error',
        'no-new-func': 'error',
        'no-new-wrappers': 'error',
        'no-nonoctal-decimal-escape': 'error',
        'no-octal': 'error',
        'no-octal-escape': 'error',
        'no-param-reassign': 'error',
        'no-proto': 'error',
        'no-restricted-properties': 'error',
        'no-return-assign': 'error',
        'no-script-url': 'error',
        'no-self-assign': 'error',
        'no-self-compare': 'error',
        'no-sequences': 'error',
        'no-unmodified-loop-condition': 'error',
        'no-useless-call': 'error',
        'no-useless-catch': 'error',
        'no-useless-concat': 'error',
        'no-useless-escape': 'error',
        'no-useless-return': 'error',
        'no-void': 'error',
        'no-with': 'error',
        'prefer-regex-literals': 'error',
        // Variables
        'no-delete-var': 'error',
        'no-shadow-restricted-names': 'error',
        'no-undef': 'error',
        'no-undef-init': 'error',
        'no-undefined': 'error',
        // Stylistic Issues
        'array-bracket-newline': ['error', 'consistent'],
        'array-bracket-spacing': 'error',
        'array-element-newline': ['error', 'consistent'],
        'block-spacing': 'error',
        'camelcase': 'error',
        'comma-style': 'error',
        'computed-property-spacing': 'error',
        'eol-last': 'error',
        'func-name-matching': 'error',
        'func-style': ['error', 'declaration', {allowArrowFunctions: true}],
        'function-call-argument-newline': ['error', 'consistent'],
        'function-paren-newline': ['error', 'consistent'],
        'key-spacing': 'error',
        'linebreak-style': 'error',
        'new-parens': 'error',
        'no-bitwise': 'error',
        'no-continue': 'error',
        'no-lonely-if': 'error',
        'no-mixed-spaces-and-tabs': 'error',
        'no-multi-assign': 'error',
        'no-multiple-empty-lines': 'error',
        'no-nested-ternary': 'error',
        'no-new-object': 'error',
        'no-tabs': 'error',
        'no-trailing-spaces': 'error',
        'no-underscore-dangle': 'error',
        'no-unneeded-ternary': 'error',
        'no-whitespace-before-property': 'error',
        'object-curly-newline': ['error', {consistent: true}],
        'operator-assignment': 'error',
        'operator-linebreak': ['error', 'before'],
        'sort-keys': 'error',
        'space-in-parens': 'error',
        'space-unary-ops': 'error',
        'switch-colon-spacing': 'error',
        // ECMAScript 6
        'arrow-body-style': 'error',
        'arrow-parens': ['error', 'as-needed'],
        'arrow-spacing': 'error',
        'constructor-super': 'error',
        'no-class-assign': 'error',
        'no-confusing-arrow': 'error',
        'no-const-assign': 'error',
        'no-new-symbol': 'error',
        'no-this-before-super': 'error',
        'no-useless-computed-key': 'error',
        'no-useless-rename': 'error',
        'no-var': 'error',
        'object-shorthand': 'error',
        'prefer-arrow-callback': 'error',
        'prefer-const': 'error',
        'prefer-rest-params': 'error',
        'prefer-template': 'error',
        'rest-spread-spacing': 'error',
        'sort-imports': 'error',
        'template-curly-spacing': 'error',
        // https://eslint.vuejs.org/rules/
        // Priority B: Strongly Recommended (Improving Readability)
        'vue/html-closing-bracket-newline': ['error', {
            multiline: 'never',
            singleline: 'never'
        }],
        'vue/html-closing-bracket-spacing': ['error', {
            endTag: 'never',
            selfClosingTag: 'never',
            startTag: 'never'
        }],
        'vue/html-indent': ['error', 4, {
            alignAttributesVertically: true,
            attribute: 1,
            baseIndent: 1,
            closeBracket: 0
        }],
        'vue/html-self-closing': ['error', {
            html: {
                component: 'always',
                normal: 'always',
                void: 'always'
            },
            math: 'always',
            svg: 'always'
        }],
        'vue/max-attributes-per-line': 'off',
        // Priority C: Recommended (Minimizing Arbitrary Choices and Cognitive Overhead)
        'vue/component-tags-order': ['error', {
            'order': ['script', 'template', 'style']
        }],
        // Uncategorized
        'vue/block-lang': ['error', {script: {lang: 'ts'}}],
        'vue/block-tag-newline': ['error', {
            maxEmptyLines: 0,
            multiline: 'always',
            singleline: 'always'
        }],
        'vue/component-api-style': ['error', ['script-setup']],
        'vue/component-name-in-template-casing': ['error', 'PascalCase', {
            ignores: ['component'],
            registeredComponentsOnly: false
        }],
        'vue/custom-event-name-casing': ['error', 'camelCase'],
        'vue/next-tick-style': ['error', 'promise'],
        'vue/no-boolean-default': ['error'],
        'vue/no-deprecated-v-is': ['error'],
        'vue/no-empty-component-block': ['error'],
        'vue/no-export-in-script-setup': ['error'],
        'vue/no-invalid-model-keys': ['error'],
        'vue/no-multiple-objects-in-class': ['error'],
        'vue/no-potential-component-option-typo': ['error', {presets: ['all']}],
        'vue/no-reserved-component-names': ['error', {
            disallowVue3BuiltInComponents: true,
            disallowVueBuiltInComponents: true
        }],
        'vue/no-static-inline-styles': ['error'],
        'vue/no-template-target-blank': ['error'],
        'vue/no-this-in-before-route-enter': ['error'],
        // 'vue/no-undef-properties': ['error'],
        'vue/no-unused-properties': ['error'],
        'vue/no-unused-refs': ['error'],
        'vue/no-use-computed-property-like-method': ['error'],
        'vue/no-useless-mustaches': ['error'],
        'vue/no-useless-template-attributes': ['error'],
        'vue/no-useless-v-bind': ['error'],
        'vue/no-v-text': ['error'],
        'vue/padding-line-between-blocks': ['error', 'always'],
        'vue/require-emit-validator': ['error'],
        'vue/script-indent': ['error', 4, {baseIndent: 1, switchCase: 1}],
        'vue/static-class-names-order': ['error'],
        'vue/v-for-delimiter-style': ['error'],
        'vue/v-on-event-hyphenation': ['error', 'always', {autofix: true}],
        'vue/v-on-function-call': ['error'],
        'vue/valid-define-emits': ['error'],
        // 'vue/valid-define-props': ['error'],
        'vue/valid-next-tick': ['error'],
        'vue/valid-v-memo': ['error'],
        // Extension Rules
        'vue/array-bracket-newline': ['error', 'consistent'],
        'vue/array-bracket-spacing': 'error',
        'vue/arrow-spacing': 'error',
        'vue/block-spacing': 'error',
        'vue/brace-style': 'error',
        'vue/camelcase': 'error',
        'vue/comma-dangle': 'error',
        'vue/comma-spacing': 'error',
        'vue/comma-style': 'error',
        'vue/dot-location': ['error', 'property'],
        'vue/dot-notation': 'error',
        'vue/eqeqeq': 'error',
        'vue/func-call-spacing': 'error',
        'vue/key-spacing': 'error',
        'vue/keyword-spacing': 'error',
        'vue/no-constant-condition': 'error',
        'vue/no-empty-pattern': 'error',
        'vue/no-extra-parens': 'error',
        'vue/no-irregular-whitespace': 'error',
        'vue/no-sparse-arrays': 'error',
        'vue/no-useless-concat': 'error',
        'vue/object-curly-newline': ['error', {consistent: true}],
        'vue/object-curly-spacing': 'error',
        'vue/operator-linebreak': ['error', 'before'],
        'vue/prefer-template': 'error',
        'vue/space-in-parens': 'error',
        'vue/space-infix-ops': 'error',
        'vue/space-unary-ops': 'error',
        'vue/template-curly-spacing': 'error',
        // https://github.com/typescript-eslint/typescript-eslint/tree/master/packages/eslint-plugin
        // Supported Rules
        '@typescript-eslint/array-type': 'error',
        '@typescript-eslint/ban-tslint-comment': 'error',
        '@typescript-eslint/class-literal-property-style': 'error',
        '@typescript-eslint/consistent-indexed-object-style': 'error',
        '@typescript-eslint/consistent-type-imports': 'error',
        '@typescript-eslint/explicit-function-return-type': 'error',
        '@typescript-eslint/explicit-member-accessibility': 'error',
        '@typescript-eslint/method-signature-style': 'error',
        '@typescript-eslint/no-base-to-string': 'error',
        '@typescript-eslint/no-confusing-non-null-assertion': 'error',
        '@typescript-eslint/no-confusing-void-expression': 'error',
        '@typescript-eslint/no-extraneous-class': 'error',
        '@typescript-eslint/no-implicit-any-catch': 'error',
        '@typescript-eslint/no-inferrable-types': ['error', {ignoreProperties: true}],
        '@typescript-eslint/no-invalid-void-type': 'error',
        '@typescript-eslint/no-meaningless-void-operator': 'error',
        '@typescript-eslint/no-non-null-asserted-nullish-coalescing': 'error',
        '@typescript-eslint/no-require-imports': 'error',
        '@typescript-eslint/no-unnecessary-boolean-literal-compare': 'error',
        '@typescript-eslint/no-unnecessary-condition': 'error',
        '@typescript-eslint/no-unnecessary-qualifier': 'error',
        '@typescript-eslint/no-unnecessary-type-arguments': 'error',
        '@typescript-eslint/no-unnecessary-type-constraint': 'error',
        '@typescript-eslint/no-unsafe-argument': 'error',
        '@typescript-eslint/non-nullable-type-assertion-style': 'error',
        '@typescript-eslint/prefer-for-of': 'error',
        '@typescript-eslint/prefer-function-type': 'error',
        '@typescript-eslint/prefer-literal-enum-member': 'error',
        '@typescript-eslint/prefer-optional-chain': 'error',
        '@typescript-eslint/prefer-readonly': 'error',
        // '@typescript-eslint/prefer-readonly-parameter-types': 'error',
        '@typescript-eslint/prefer-reduce-type-parameter': 'error',
        '@typescript-eslint/prefer-return-this-type': 'error',
        '@typescript-eslint/prefer-string-starts-ends-with': 'error',
        '@typescript-eslint/promise-function-async': 'error',
        '@typescript-eslint/sort-type-union-intersection-members': 'error',
        '@typescript-eslint/strict-boolean-expressions': 'error',
        '@typescript-eslint/type-annotation-spacing': 'error',
        '@typescript-eslint/unified-signatures': 'error',
        // Extension Rules
        '@typescript-eslint/brace-style': 'error',
        '@typescript-eslint/comma-dangle': 'error',
        '@typescript-eslint/comma-spacing': 'error',
        '@typescript-eslint/default-param-last': 'error',
        '@typescript-eslint/dot-notation': 'error',
        '@typescript-eslint/func-call-spacing': 'error',
        '@typescript-eslint/indent': ['error', 4],
        '@typescript-eslint/init-declarations': 'error',
        '@typescript-eslint/keyword-spacing': 'error',
        '@typescript-eslint/lines-between-class-members': ['error', 'always', {exceptAfterSingleLine: true}],
        '@typescript-eslint/no-array-constructor': 'error',
        '@typescript-eslint/no-dupe-class-members': 'error',
        '@typescript-eslint/no-duplicate-imports': 'error',
        '@typescript-eslint/no-empty-function': 'error',
        '@typescript-eslint/no-extra-parens': ['error', 'all', {enforceForArrowConditionals: false}],
        '@typescript-eslint/no-extra-semi': 'error',
        '@typescript-eslint/no-implied-eval': 'error',
        '@typescript-eslint/no-invalid-this': 'error',
        '@typescript-eslint/no-loop-func': 'error',
        '@typescript-eslint/no-loss-of-precision': 'error',
        '@typescript-eslint/no-redeclare': 'error',
        '@typescript-eslint/no-shadow': 'error',
        '@typescript-eslint/no-unused-expressions': 'error',
        '@typescript-eslint/no-unused-vars': 'error',
        '@typescript-eslint/no-use-before-define': 'error',
        '@typescript-eslint/no-useless-constructor': 'error',
        '@typescript-eslint/object-curly-spacing': 'error',
        '@typescript-eslint/quotes': ['error', 'single'],
        '@typescript-eslint/return-await': 'error',
        '@typescript-eslint/semi': ['error', 'never'],
        '@typescript-eslint/space-before-function-paren': ['error', {
            anonymous: 'always',
            asyncArrow: 'always',
            named: 'never'
        }],
        '@typescript-eslint/space-infix-ops': 'error'
    }
}
