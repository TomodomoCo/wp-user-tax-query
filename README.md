# WP User Tax Query

A simple drop-in plugin to support a limited version of the `tax_query` parameter inside a `WP_User_Query`.

## Caveats

+ You can't — and should not — supply a `relation`; `AND` is forced. (`OR` could be supported by replacing `array_intersect` with `array_merge`, if that's important for your use case)
+ Because relations aren't supported, you also can't do nested taxonomy queries
+ You can still provide an `include` argument on your `WP_User_Query` to scope your results to a specific corpus of user IDs, but `nicename__(in|not_in)` and `login__(in|not_in)` may not work (they haven't been tested)
+ There may be unexpected behaviors with other query parameters
+ This will multiply the number of SQL queries run on each request; you should probably cache results

## Why not…

There are typically two methods floated around on the web for using a taxonomy query within a user query.

The one that _seems_ correct leverages `get_tax_sql`, a function WordPress uses to generate taxonomy SQL queries for posts. However, I could not get it to behave correctly when used in tandem with `WP_User_Query`. I expect this relates to some way that `WP_User_Query`'s SQL deviates from `WP_Query`, but in the interest of time and sanity, opted to implement it this way instead.

## About Tomodomo

Tomodomo is a creative agency for magazine publishers. We use custom design and technology to speed up your editorial workflow, engage your readers, and build sustainable subscription revenue for your business.

Learn more at [tomodomo.co](https://tomodomo.co) or email us: [hello@tomodomo.co](mailto:hello@tomodomo.co)

## License & Conduct

This project is licensed under the terms of the MIT License, included in `LICENSE.md`.

All open source Tomodomo projects follow a strict code of conduct, included in `CODEOFCONDUCT.md`. We ask that all contributors adhere to the standards and guidelines in that document.

Thank you!