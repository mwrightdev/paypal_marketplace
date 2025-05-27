# PayPal Marketplace Plugin for Elgg

A powerful marketplace plugin for Elgg that enables users to buy, sell, rent, trade, auction, gift, and donate items with PayPal integration.

## Features

- **Multiple Transaction Types**
  - Buy/Sell: Direct purchase of items
  - Rent: Time-based rentals with customizable periods (hour/day/week/month)
  - Trade: Item exchange with descriptions
  - Auction: Time-based auctions with minimum bids
  - Gift/Donate: Free item transfers with optional anonymity

- **Shopping Cart System**
  - Add/remove items to cart
  - Cart management
  - Secure checkout process

- **PayPal Integration**
  - Secure payment processing
  - Automatic seller payouts
  - Platform fee management
  - Transaction tracking
  - Order status management

- **User Features**
  - Item listings with images
  - Multiple currency support (USD, EUR, GBP, CAD, AUD, JPY)
  - Price management
  - Item descriptions
  - Transaction history
  - Buyer/Seller notifications

## Requirements

- Elgg 4.x or higher
- PHP 7.4 or higher
- PayPal Business Account
- PayPal API credentials

## Installation

1. Download and extract the plugin to your Elgg's `mod` directory
2. Enable the plugin through Elgg's admin interface
3. Configure PayPal API credentials in the plugin settings
4. Set up platform fee percentage (if applicable)

## Configuration

### PayPal Settings
- Client ID
- Client Secret
- Platform Fee Percentage
- Sandbox Mode (for testing)

### Marketplace Settings
- Allowed currencies
- Transaction types
- Image upload limits
- Access permissions

## Usage

### For Sellers
1. Create a new listing
2. Select transaction type
3. Set price and currency
4. Add images and description
5. Configure type-specific settings (e.g., rent period, auction end date)
6. Publish listing

### For Buyers
1. Browse marketplace listings
2. Add items to cart
3. Review cart
4. Proceed to PayPal checkout
5. Complete payment
6. Receive confirmation and notifications

## Transaction Types

### Buy/Sell
- Direct purchase transactions
- Immediate payment processing
- Automatic seller payout

### Rent
- Time-based rentals
- Configurable periods (hour/day/week/month)
- Secure payment handling

### Trade
- Item exchange system
- Detailed trade descriptions
- Mutual agreement process

### Auction
- Time-based auctions
- Minimum bid settings
- Automatic end date handling
- Highest bidder wins

### Gift/Donate
- Free item transfers
- Optional anonymous mode
- Direct user-to-user transfer

## Security

- Secure PayPal integration
- User authentication
- Access control
- Transaction verification
- Data encryption

## Support

For support, please:
1. Check the [Elgg Community Forums](https://elgg.org/discussion)
2. Create an issue in the plugin's repository
3. Contact the plugin maintainer

## License

This plugin is released under the GNU General Public License v2.0.

## Credits

Developed for Elgg by [Your Name/Organization]
