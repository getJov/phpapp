# Add Login Password Toggle

## Why
The login form still has a plain password field while signup and user forms already have show/hide controls. This creates inconsistent password UX.

## What
Add a show/hide button to the login password field using the existing `data-toggle-password` JavaScript behavior.

## Constraints

**Must:**
- Use `type="button"` so the toggle cannot submit the form.
- Reuse the existing JavaScript toggle pattern.
- Preserve login validation and authentication behavior.

**Must not:**
- Add dependencies.
- Change password validation rules for login.
- Touch unrelated forms.

## Risk

**Level:** 1

**Risks identified:**
- Toggle button could accidentally submit the form -> **Mitigation:** use `type="button"`.

**Pushback:**
- None.

## Tasks

### T1: Login Password Toggle
**Do:** Wrap the login password input in a Bootstrap input group and add a show/hide button wired to `data-toggle-password="password"`.
**Files:** `login.php`
**Verify:** `php -l login.php`; manual: button toggles password visibility without submitting.

## Done
- [ ] `php -l login.php` passes.
- [ ] Manual: login page responds from local server.
- [ ] Manual: show/hide button exists on login password field.
- [ ] No authentication behavior changes.
