# CSE historical

This is a PHP script to download historical data from the Canadian Securities Exchange (CSE, formerly CNSX). It will give you a bunch of txt files, one for each trading day, containing trading data for all securities on that date.

# Trivialities

The oldest file available is from 2005-05-24.

On 2008-10-17 the format changed to always include a tab character between columns. This script handles both the old and the newer formats.

# Unclean data

Some CSE daily summary files are messed up so you will need to either fix them manually or use the files provided in the `fixed_daily_summaries` folder.