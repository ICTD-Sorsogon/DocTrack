import { extend } from 'vee-validate';
import { required, email, max, min, confirmed, alpha_spaces, numeric } from 'vee-validate/dist/rules';

extend ('required', {
  ...required,
  message: 'This field is required'
});

extend ('max', {
  ...max,
  message: 'This field must be {length} characters or less'
});

extend ('min', {
  ...min,
  message: 'This field must be {length} characters or more'
});

extend ('email', {
  ...email,
  message: 'This field must be a valid email'
});


extend ('confirmed', {
  ...confirmed,
  message: 'The value for {_field_} field does not match'
});


extend ('password', {
  params: ['target'],
  validate(value, { target }) {
    return value === target;
  },
  message: 'Password confirmation does not match'
});

extend ('alpha_spaces', {
  ...alpha_spaces,
  message: 'Suffix may only contain alphabetic characters'
})

extend ('numeric', {
  ...numeric,
  message: 'This field must only contain numerical values'
});
