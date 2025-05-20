# NYT Best Sellers API Wrapper

This project is a wrapper around the New York Times Best Sellers Overview API endpoint, it provides a clean interface to interact with the NYT Best Sellers data while implementing performance optimizations.

## Technical Implementation

### Performance Optimizations

The API wrapper implements two key performance optimizations:

1. **Throttling**: This helps maintain a sustainable rate of API calls while preventing potential rate limiting issues. Throttling rate can be adjust in the `.env` file.

2. **Caching**: The wrapper implements a caching layer to store frequently accessed data. This reduces the number of direct API calls to the NYT servers, resulting in:
   - Faster response times for repeated requests
   - Reduced load on the NYT API servers
   - Lower bandwidth usage

### Testing

Full test coverage is provided in 2 separate feature test files.

## API Endpoint

- `/api/v1/nyt/bestsellers/overview`

### The following query parameters are accepted

1. author
2. isnb[]
3. title
4. published date (YYYY-MM-DD)
