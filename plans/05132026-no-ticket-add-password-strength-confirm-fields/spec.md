# Add Password Strength And Confirm Fields

## Why
Current password handling only requires six characters and gives users no feedback before submission. Add clear password requirements, confirmation, and visibility controls so weak or mismatched passwords are caught early and consistently.

## What
Signup and user password forms enforce LUDS8: at least one lowercase letter, uppercase letter, digit, symbol, and at least 8 characters. Password inputs show a colored strength meter, include show/hide controls, and require a matching confirm password field wherever a new password is required or provided.

## Context

**Relevant files:**
- `signup.php` - public signup form and server-side account creation validation.
- `users/create.php` - dashboard create-user form and server-side password validation.
- `users/edit.php` - dashboard edit-user form where password is optional.
- `assets/js/app.js` - existing lightweight JS for confirmation prompts.
- `assets/css/styles.css` - existing small custom styling layer.

**Patterns to follow:**
- Keep plain PHP page-level form handling.
- Keep validation server-side first, with JavaScript only as user feedback.
- Reuse Bootstrap form controls and simple custom CSS.
- Avoid introducing dependencies.

**Key decisions already made:**
- LUDS8 means lowercase, uppercase, digit, symbol, and minimum 8 characters.
- Symbol means any non-alphanumeric character, enforced with `/[^A-Za-z0-9]/`.
- Password confirmation is required on `signup.php` and `users/create.php`.
- On `users/edit.php`, password remains optional; if password is blank, confirm password may be blank and the existing password is kept. If password is filled, confirm password is required and must match.
- On `users/edit.php`, if either password or confirm password is filled without a valid matching pair, reject the request.
- Show/hide controls apply to both password and confirm password inputs, use `type="button"`, and toggle only their paired input.
- Password and confirm password inputs always render empty after validation errors.
- Meter states are criteria-based: 0-1 criteria = red/weak, 2-3 = yellow/fair, 4 = blue/good, 5 = green/strong.

## Constraints

**Must:**
- Enforce LUDS8 on the server before hashing.
- Enforce confirm password matching on the server.
- Preserve existing CSRF protection and prepared statements.
- Preserve optional password behavior on edit.
- Escape all rendered user-controlled values.
- Keep the UI simple and Bootstrap-compatible.
- Use the same LUDS8 criteria in JavaScript and PHP.

**Must not:**
- Treat the JavaScript meter as security.
- Add libraries or framework code.
- Change database schema.
- Break existing login behavior.
- Expose password values after failed validation.

**Out of scope:**
- Password reset.
- Password history.
- Account lockout.
- Role-based password policy.

## Risk

**Level:** 2

**Risks identified:**
- Client-side strength meters are easy to bypass -> **Mitigation:** implement the same LUDS8 rule in PHP before insert/update.
- Duplicating password validation across pages can drift -> **Mitigation:** add a shared helper function in `includes/auth.php` or a small shared include and reuse it from signup/create/edit.
- Edit-user password is optional, which can accidentally become required -> **Mitigation:** explicitly branch validation only when the edit password field is non-empty.
- Show/hide buttons inside forms can accidentally submit forms -> **Mitigation:** require `type="button"` on visibility toggle buttons.

**Pushback:**
- A pretty meter without server validation is security theater. The actual contract is the PHP validation; the meter is just feedback.

## Tasks

### T1: Shared Password Validation
**Do:** Add shared validation helpers for LUDS8 and confirm-password matching. Reuse them from signup, create-user, and edit-user flows. Treat symbols as `/[^A-Za-z0-9]/`. On edit, keep the existing password only when password and confirm password are both blank; reject any partial or mismatched pair.
**Files:** `includes/auth.php`, `signup.php`, `users/create.php`, `users/edit.php`
**Verify:** `php -l` on touched PHP files; manual invalid passwords are rejected server-side.

### T2: Password UI Enhancements
**Do:** Add confirm password fields, show/hide buttons for password and confirm password, and a colored LUDS8 strength indicator on forms that accept new passwords. Toggle buttons must be `type="button"`. Password and confirm password inputs must not be repopulated after validation errors. Meter colors: red/weak for 0-1 criteria, yellow/fair for 2-3, blue/good for 4, green/strong for 5.
**Files:** `signup.php`, `users/create.php`, `users/edit.php`, `assets/js/app.js`, `assets/css/styles.css`
**Verify:** Manual: meter updates while typing, show/hide toggles both fields, confirm mismatch prevents submission server-side.

## Done
- [ ] `php -l` passes for all PHP files.
- [ ] Manual: signup rejects passwords missing lowercase, uppercase, digit, symbol, or 8-character minimum.
- [ ] Manual: create-user rejects passwords missing lowercase, uppercase, digit, symbol, or 8-character minimum.
- [ ] Manual: edit-user keeps existing password when password and confirm password are blank.
- [ ] Manual: edit-user rejects confirm password when password is blank.
- [ ] Manual: edit-user rejects weak or mismatched new passwords when password is provided.
- [ ] Manual: password meter shows colored strength feedback.
- [ ] Manual: show/hide buttons work for password and confirm password fields.
- [ ] Manual: password and confirm password fields are empty after validation errors.
- [ ] No database schema changes.

## Revision Log

### Rev 1 - May 13, 2026
**Change:** Defined symbol matching, meter color states, edit-form partial-password rejection, show/hide button behavior, password field clearing after errors, and full PHP lint verification.
**Reason:** The original spec left small but execution-relevant behavior ambiguous.
**Updated Done criteria:** Added full PHP lint, edit confirm-without-password rejection, and empty password field verification.
