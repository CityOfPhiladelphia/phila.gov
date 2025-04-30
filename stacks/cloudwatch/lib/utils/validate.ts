import assert from 'assert';

const environment = (envars: string[]) => {
    return envars.reduce((acc: { [envar: string]: string | undefined }, curr) => {
      const value = process.env[curr];
      assert(value, `Environment variable ${curr} is required. Current value is ${value}`);
      acc[curr] = value;
      return acc;
    }, {});
  }

export const validate = {
    environment,
};
